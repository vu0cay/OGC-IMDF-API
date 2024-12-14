<?php

namespace App\Http\Resources\FeatureResources;

use App\Constants\Features\TablesName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table(TablesName::DETAILS . ' as detail')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'))
            ->where('detail.detail_id', '=', $this->detail_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);

        return [
            "id" => $this->detail_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "level_id" => $this->level_id
            ]
        ];
    }
}
