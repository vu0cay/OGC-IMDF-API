<?php

namespace App\Http\Resources\FeatureResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        


        return [
            "id" => $this->address_id,
            "type" => "Feature",
            "feature_type" => $this->featuretest->feature_type,
            "geometry" => null,
            "properties" => [
                "address" => $this->address,
                "unit" => $this->unit,
                "locality" => $this->locality,
                "province" => $this->province,
                "country" => $this->country,
                "postal_code" => $this->postal_code,
                "postal_code_ext" => $this->postal_code_ext
            ]
        ];
    }
}
