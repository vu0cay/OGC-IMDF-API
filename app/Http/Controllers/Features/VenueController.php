<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\TablesName;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\VenueResource;
use App\Models\Features\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        // $venues = Venue::with('feature', 'restriction', 'category', 'labels')->get();
        $venues = Venue::get();
        $venuesResource = VenueResource::collection($venues);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
    public function show($venue_id) {

        $venue = Venue::query()
                    // ->with('feature', 'restriction', 'category')
                    ->where('venue_id', '=',$venue_id)->first();
        
        if (!$venue) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);


        $venuesResource = VenueResource::collection([$venue]);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
