<?php

namespace App\Http\Resources\FeatureResources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FootprintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('footprints as footprint')
            ->join('features as f', 'f.feature_id', '=', 'footprint.footprint_id')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
            ->where('footprint.footprint_id', '=', $this->footprint_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        
        return [
            "id" => $this->footprint_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category,
                "name" => $this->labels->pluck('value', 'language_tag' )->toArray(),
                "building_ids" => $this->buildings->pluck('building_id')->toArray()
            ]
        ];
    }
}
