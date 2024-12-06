<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
use DB;
use geoPHP;
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
 
        // using ST_AsGeoJson in POSTGIS to convert WKB to GeoJson geometry 
        $geom = DB::table('amenities as amenity')
        ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
        ->where('amenity.amenity_id', '=', $this->amenity_id)
        ->get();
    
        $geometry = json_decode($geom[0]->geometry);

        $name = GetFeatureName::getName($this->labels, 'value');
        $alt_name = GetFeatureName::getName($this->labels, 'short_name');

        return [
            "id" => $this->amenity_id,
            "type" => 'Feature',
            "feature_type" => $this->feature->feature_type,
            "geometry" => $geometry,
            "properties" => [
                "category" => $this->category->name,
                "accessibility" =>  count($this->accessibilities) > 0 ? $this->accessibilities->pluck('name')->toArray() : null,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name!=null &&  count($alt_name) > 0 ? $alt_name : null,
                "phone" => $this->phone,
                "website" => $this->website,
                "hours" => $this->hours,
                'address_id' => $this->address->address_id ?? null, 
                "unit_ids" => count($this->units) ? $this->units->pluck('unit_id')->toArray() : null,
                "correlation_id" => $this->correlation_id
            ]
        ];
    }
}
