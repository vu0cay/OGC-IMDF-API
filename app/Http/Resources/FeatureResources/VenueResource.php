<?php

namespace App\Http\Resources\FeatureResources;

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
            ->join('features as f', 'f.feature_id', '=', 'v.venue_id')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('v.id', '=', $this->id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        // // convert geometry data in postgres (display point) to geojson data format
        $display_point = json_decode($geom[0]->display_point);



        // tach ra thanh 1 ham rieng getName
        // $name = DB::table('venues as venue')
        //     ->select('label.language_tag as language_tag', 'label.value as value')
        //     ->join('venue_label as venue_label', 'venue.venue_id', '=', 'venue_label.venue_id')
        //     ->join('labels as label', 'label.id', '=', 'venue_label.label_id')
        //     ->where('venue.venue_id', '=', $this->venue_id)
        //     ->get()
        //     ->pluck('value', 'language_tag')
        //     ->toArray();
        
            
        return [
            "id" => $this->venue_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "restriction" => $this->restriction->name ?? null,
                "name" => $this->labels->pluck( 'value', 'language_tag')->toArray(),
                "alt_name" => null,
                "hours" => $this->hours,
                "website" => $this->website,
                "phone" => $this->phone,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address_id
            ]

        ];
    }
}
