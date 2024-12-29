<?php

namespace App\Http\Resources\FeatureResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManifestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'version' => $this->version,
            'created' => $this->created_at,
            'generated_by' => $this->generated_by ?? null,
            'language' => $this->language,
            "extensions" => [ "imdf:extension:big-company:internal#1.0.0" ]
        ];
    }
}
