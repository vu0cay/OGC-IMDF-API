<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AmenityResource;
use App\Models\Features\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $amenities = Amenity::with('feature', 'units', 'category', 'labels', 'accessibilities')->get();
        $amenities = Amenity::get();
        $amenitiesResource = AmenityResource::collection($amenities);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $amenitiesResource;

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
    public function show($amenity_id)
    {
        $amenity = Amenity::query()
                    ->where('amenity_id', '=', $amenity_id)->first();
        
        if(!$amenity) return response()->json( ['success' => false, 'message'=> 'Not Found'],404);


        $amenitysResource = AmenityResource::collection([$amenity]);

        // $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

        $geojson = json_decode($geojson);
        $geojson->features = $amenitysResource;

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
