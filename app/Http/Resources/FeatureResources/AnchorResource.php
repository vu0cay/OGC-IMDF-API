<?php

namespace App\Http\Resources\FeatureResources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnchorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('anchors as anchor')
            ->join('features as f', 'f.feature_id', '=', 'anchor.anchor_id')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
            ->where('anchor.anchor_id', '=', $this->anchor_id)
            ->get();
        
        $geometry = json_decode($geom[0]->geometry);

        return [
            "id" => $this->anchor_id,
            "type" => $this->feature->type,
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "address_id" => $this->address_id,
                "unit_id" => $this->unit_id
            ]
        ];
    }
}
