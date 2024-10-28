<?php

namespace App\Http\Resources\FeatureResources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmenityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('amenities as amenity')
        ->join('features as f', 'f.feature_id', '=', 'amenity.amenity_id')
        ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
        ->where('amenity.amenity_id', '=', $this->amenity_id)
        ->get();
    
        $geometry = json_decode($geom[0]->geometry);
        
        return [
            "id" => $this->amenity_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "accessibility" => $this->accessibilities->pluck('name')->toArray(),
                "name" => $this->labels->pluck('value', 'language_tag')->toArray(),
                // "alt_name" => null,
                "phone" => $this->phone,
                "website" => $this->website,
                "hours" => $this->hours,
                "address_id" => $this->address_id,
                "unit_ids" => $this->units->pluck('unit_id')->toArray(),
                "correlation_id" => $this->correlation_id
            ]
        ];
    }
}
