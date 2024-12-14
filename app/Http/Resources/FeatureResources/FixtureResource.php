<?php

namespace App\Http\Resources\FeatureResources;

use App\Constants\Features\TablesName;
use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table(TablesName::FIXTURES.' as fixture')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('fixture.fixture_id', '=', $this->fixture_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);
        $display_point = json_decode($geom[0]->display_point);


        $name = GetFeatureName::getName($this->labels, 'name');
        $alt_name = GetFeatureName::getName($this->labels, 'alt_name');
        // dd($this->category);
        return [
            "id" => $this->fixture_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name,
                "name" => $name != null && count($name) > 0 ? $name : null,
                "alt_name" => $alt_name != null && count($alt_name) > 0 ? $alt_name : null,
                "display_point" => $display_point !== null ? [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ] : null,
                "anchor_id" => $this->anchor_id ?? null,
                "level_id" => $this->level_id

            ]

        ];
    }
}
