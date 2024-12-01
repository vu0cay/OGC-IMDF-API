<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\BuildingResource;
use App\Models\Features\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buildings = Building::with('feature', 'restriction', 'category')->get();
        $buildingsResource = BuildingResource::collection($buildings);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;
        
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
    public function show($building_id)
    {
        $buildings = Building::query()
                            ->where('building_id', '=', $building_id)
                            ->with('feature', 'restriction', 'category')
                            ->first();
        $buildingsResource = BuildingResource::collection([$buildings]);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;
        
        return $geojson;
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
