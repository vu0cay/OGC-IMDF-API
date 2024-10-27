<?php

namespace App\Http\Resources\FeatureResources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('buildings as b')
            ->join('features as f', 'f.feature_id', '=', 'b.building_id')
            ->select( DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('building_id', '=', $this->building_id)
            ->get();

        // // convert geometry data in postgres (display point) to geojson data format
        $display_point = json_decode($geom[0]->display_point);

        // tach ra thanh 1 ham rieng getName
        $name = DB::table('buildings as b')
            ->select('label.language_tag as language_tag', 'label.value as value')
            ->join('building_label as building_label', 'b.building_id', '=', 'building_label.building_id')
            ->join('labels as label', 'label.id', '=', 'building_label.label_id')
            ->where('b.building_id', '=', $this->building_id)
            ->get()
            ->pluck('value', 'language_tag')
            ->toArray();


        return [
            "id" => $this->building_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => null,
            "properties" => [
                "category" => $this->category->name,
                "restriction" => $this->restriction->name ?? null,
                "name" => $name,
                "alt_name" => null,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address_id
            ]
        ];
    }
}
