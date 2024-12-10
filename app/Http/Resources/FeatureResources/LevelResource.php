<?php

namespace App\Http\Resources\FeatureResources;

use App\Contracts\GetFeatureName;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $geom = DB::table('levels as level')
            ->select(DB::raw('ST_AsGeoJson(geometry) as geometry'), DB::raw('ST_AsGeoJson(display_point) as display_point'))
            ->where('level.level_id', '=', $this->level_id)
            ->get();

        // // convert geometry data in postgres (geometry coordinates) to geojson data format
        $geometry = json_decode($geom[0]->geometry);

        $display_point = json_decode($geom[0]->display_point);


        // $name = GetFeatureName::getName($this->labels, 'value');
        // $short_name = GetFeatureName::getName($this->labels, 'short_name');
        $name = count($this->labels) > 0 ? 
                    $this->labels
                    ->filter(function ($item){
                        return $item->pivot->type == 'name';
                    })
                    ->pluck('value', 'language_tag')->toArray() 
                    : null;
        
        $short_name = count($this->labels) > 0 ? 
                    $this->labels
                    ->filter(function ($item){
                        return $item->pivot->type == 'short_name';
                    })
                    ->pluck('value', 'language_tag')->toArray() 
                    : null;

        return [
            "id" => $this->level_id,
            "type" => "Feature",
            "feature_type" => $this->feature->feature_type,
            "geometry" => [
                "type" => $geometry->type,
                "coordinates" => $geometry->coordinates
            ],
            "properties" => [
                "category" => $this->category->name ?? null,
                "restriction" => $this->restriction->name ?? null,
                "ordinal" => $this->ordinal,
                "outdoor" => $this->outdoor,
                "name" => $name!=null && count($name) > 0 ? $name : null,
                "short_name" => $short_name !=null &&  count($short_name) > 0 ? $short_name : null,
                "display_point" => [
                    "type" => $display_point->type,
                    "coordinates" => $display_point->coordinates
                ],
                "address_id" => $this->address->address_id ?? null,
                "building_ids" => count($this->buildings) > 0 ? $this->buildings->pluck('building_id')->toArray() : null
            ]
        ];
    }
}
