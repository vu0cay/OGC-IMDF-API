<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
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
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
            ->where('footprint.footprint_id', '=', $this->footprint_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        
        $name = GetFeatureName::getName($this->labels, 'value');

        return [
            "id" => $this->footprint_id,
            "type" => "Feature",
            "feature_type" => $this->featuretest->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name ?? null,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "building_ids" => $this->buildings->pluck('building_id')->toArray()
            ]
        ];
    }
}
