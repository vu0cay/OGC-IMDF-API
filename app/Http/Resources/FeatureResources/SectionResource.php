<?php

namespace App\Http\Resources\FeatureResources;

use App\Constants\Features\TablesName;
use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $collect = [];
        foreach ($this->parents as $parent) { 
            $collect[] = $parent->section_id;
        }
        // dd($collect);

        $geom = DB::table(TablesName::SECTIONS.' as section')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('section.section_id', '=', $this->section_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        $display_point = json_decode($geom[0]->display_point);


        $name = GetFeatureName::getName($this->labels, 'name');
        $alt_name = GetFeatureName::getName($this->labels, 'alt_name');

        return [
            "id" => $this->section_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "restriction" => $this->restriction->name ?? null,
                "accessibility" => count($this->accessibilities) > 0 ? $this->accessibilities->pluck('name')->toArray() : null,

                "name" => $name != null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name != null && count($alt_name) > 0 ? $alt_name : null,

                "display_point" => $display_point !== null ? [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ] : null,
                "level_id" => $this->level_id,
                "address_id" => $this->address->address_id ?? null,
                "correlation_id" => $this->correlation_id ?? null,
                "parents" => count($collect) > 0 ? $collect : null
            ]

        ];
    }
}
