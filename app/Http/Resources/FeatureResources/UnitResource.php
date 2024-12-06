<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource 
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('units as unit')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('unit.unit_id', '=', $this->unit_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        $display_point = json_decode($geom[0]->display_point);
        

        $name = GetFeatureName::getName($this->labels, 'value');
        $alt_name = GetFeatureName::getName($this->labels, 'short_name');

   

        return [
            "id" => $this->unit_id,
            "type" => "Feature",
            "feature_type" => $this->featuretest->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "restriction" => $this->restriction->name ?? null,
                "accessibility" => count($this->accessibilities) > 0 ? $this->accessibilities->pluck('name')->toArray() : null, 
                
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name!=null &&  count($alt_name) > 0 ? $alt_name : null,

                "display_point" => $display_point !== null ? [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ] : null,
                "level_id" => $this->level_id
                
            ]

        ];
    }
}
