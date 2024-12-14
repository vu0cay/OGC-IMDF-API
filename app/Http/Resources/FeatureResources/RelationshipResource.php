<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\FeatureReferenceRelation;
use App\Models\Features\Feature;
use App\Models\FeaturesCategory\FeatureReference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $origin = isset($this->origin) ? FeatureReferenceRelation::getFeatureRelation($this->origin) : null;
        $intermediary = isset($this->intermediary) ? FeatureReferenceRelation::getFeatureIntermerdiary($this->intermediary) : null;
        $destination = isset($this->destination) ? FeatureReferenceRelation::getFeatureRelation($this->destination) : null;
        
        return [
            "id" => $this->relationship_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => null,
            "properties" => [
                "category" => $this->category->name,
                "direction" => $this->direction,
                "hours" => $this->hours ?? null,
                "origin" => $origin,
                "intermediary" => count($intermediary) > 0 ? $intermediary : null,
                "destination" => $destination,
            ]
        ];
    }
}
