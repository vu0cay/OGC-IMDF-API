<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\UnitCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\UnitResource;
use App\Models\Features\Feature;
use App\Models\Features\Unit;
use DB;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
        $unitsResource = UnitResource::collection($units);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $unitsResource;

        // $reflectionClass = new \ReflectionClass(UnitCategory::class);

        // $constants = $reflectionClass->getConstants();
        // $arr = array_values($constants);
        // dd($arr);
        
        // $feature = Feature::
        //     join('feature_label as feature_label', 'features.feature_id', '=', 'feature_label.feature_id')
        //     ->join('labels as label', 'label.id', '=', 'feature_label.label_id')
        //     // ->where('venue.venue_id', '=', $this->venue_id)
        //     ->get()
        //     // ->pluck('value', 'language_tag')
        //     ->toArray();
            
        
        // return $feature;
        return $geojson;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
