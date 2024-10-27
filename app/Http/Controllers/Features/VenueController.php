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
        
        $venues = Venue::with('feature', 'restriction', 'category')->get();
        $venuesResource = VenueResource::collection($venues);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
    public function show($venue_id) {

        $venue = Venue::query()
                    ->with('feature', 'restriction', 'category')
                    ->where('venue_id', '=',$venue_id)->first();
        $venuesResource = VenueResource::collection([$venue]);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
