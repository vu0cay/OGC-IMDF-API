<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccupantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $validity = json_decode($this->validity);
        
        $validityCol = isset($validity) ? collect([
            "start" => $validity->start,
            "end" => $validity->end,
            "modified" => $validity->modified,
        ]) : null;
        
        $name = GetFeatureName::getName($this->labels, 'name');
        // $alt_name = GetFeatureName::getName($this->labels, 'alt_name');
            
        return [
            "id" => $this->occupant_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => null,
            "properties" => [
                "category" => $this->category->name ?? null,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "phone" => $this->phone,
                "website" => $this->website,
                "hours" => $this->hours,
                "validity" => $validityCol,
                // "door" => $doorCol,

                "anchor_id" => $this->anchor_id,
                "correlation_id" => $this->correlation_id ?? null,
            ]

        ];
    }
}
