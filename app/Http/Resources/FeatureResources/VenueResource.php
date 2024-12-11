<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('venues as v')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('v.id', '=', $this->id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        // // convert geometry data in postgres (display point) to geojson data format
        $display_point = json_decode($geom[0]->display_point);

        $name = GetFeatureName::getName($this->labels, 'name');
        $alt_name = GetFeatureName::getName($this->labels, 'alt_name');

        // $name = count($this->labels) > 0 ? 
        //             $this->labels
        //             ->filter(function ($item){
        //                 return $item->pivot->type == 'name';
        //             })
        //             ->pluck('value', 'language_tag')->toArray() 
        //             : null;
        
        // $alt_name = count($this->labels) > 0 ? 
        //             $this->labels
        //             ->filter(function ($item){
        //                 return $item->pivot->type == 'alt_name';
        //             })
        //             ->pluck('value', 'language_tag')->toArray() 
        //             : null;
            
        return [
            "id" => $this->venue_id,
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
                "hours" => $this->hours,
                "website" => $this->website,
                "phone" => $this->phone,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address_id ?? null
            ]

        ];
    }
}
