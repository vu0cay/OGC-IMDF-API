<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
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
            ->select( DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('building_id', '=', $this->building_id)
            ->get();

        // // convert geometry data in postgres (display point) to geojson data format
        $display_point = json_decode($geom[0]->display_point);


        $name = GetFeatureName::getName($this->labels, 'name');
        $alt_name = GetFeatureName::getName($this->labels, 'alt_name');


                    
        return [
            "id" => $this->building_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => null,
            "properties" => [
                "category" => $this->category->name ?? null,
                "restriction" => $this->restriction->name ?? null,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name !=null &&  count($alt_name) > 0 ? $alt_name : null,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address->address_id ?? null,
            ]
        ];
    }
}
