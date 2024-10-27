<?php

namespace App\Http\Resources\FeatureResources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('levels as level')
            ->join('features as f', 'f.feature_id', '=', 'level.level_id')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('level.level_id', '=', $this->level_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);

        $display_point = json_decode($geom[0]->display_point);

        
        return [
            "id" => $this->footprint_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "restriction" => $this->restriction->name ?? null,
                "ordinal" => $this->ordinal,
                "outdoor" => $this->outdoor,
                "name" => $this->labels->pluck('value', 'language_tag' )->toArray(),
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address_id,
                "building_ids" => $this->buildings->pluck('building_id')->toArray()
                /*
                "short_name": {
                "en": "1"
                },
                */
              
            ]
        ];
    }
}
