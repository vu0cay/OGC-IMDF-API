<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\FootprintResource;
use App\Models\Features\Footprint;
use DB;
use Illuminate\Http\Request;

class FootprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $footprints = Footprint::get();
        $footprintsResource = FootprintResource::collection($footprints);
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $footprintsResource;
        
        return response()->json([$geojson], 200);
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
    public function show($footprint_id)
    {
        $footprints = Footprint::query()
                    ->where('footprint_id', '=', $footprint_id)
                    ->first();
        if (!$footprints) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);


        $footprintsResource = FootprintResource::collection([$footprints]);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $footprintsResource;
        
        return response()->json([$geojson], 200);
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
