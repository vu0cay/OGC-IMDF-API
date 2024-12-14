<?php

namespace App\Http\Resources\FeatureResources;

use App\Constants\Features\TablesName;
use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeofenceResource extends JsonResource
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
            $collect[] = $parent->geofence_id;
        }

        $geom = DB::table(TablesName::GEOFENCES.' as geofence')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('geofence.geofence_id', '=', $this->geofence_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);

        $display_point = json_decode($geom[0]->display_point);


        $name = GetFeatureName::getName($this->labels, 'name');
        $alt_name = GetFeatureName::getName($this->labels, 'alt_name');

        return [
            "id" => $this->geofence_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name ?? null,
                "restriction" => $this->restriction->name ?? null,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name !=null &&  count($alt_name) > 0 ? $alt_name : null,
                "correlation_id" => $this->correlation_id ?? null,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "building_ids" => count($this->buildings) > 0 ? $this->buildings->pluck('building_id')->toArray() : null,
                "level_ids" => count($this->levels) > 0 ? $this->levels->pluck('level_id')->toArray() : null,
                "parents" => count($collect) > 0 ? $collect : null
            ]
        ];
    }
}
