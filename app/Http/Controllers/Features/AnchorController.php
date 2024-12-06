<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AnchorResource;
use App\Models\Features\Anchor;
use Illuminate\Http\Request;

class AnchorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $anchors = Anchor::with('feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
        // $anchors = Anchor::with('feature')->get();
        $anchors = Anchor::get();
        $anchorsResource = AnchorResource::collection($anchors);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $anchorsResource;

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
    public function show($anchor_id)
    {
        $anchor = Anchor::query()
                    ->where('anchor_id', '=', $anchor_id)->first();
        if (!$anchor) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);
        
        $anchorsResource = AnchorResource::collection([$anchor]);

        // $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

        $geojson = json_decode($geojson);
        $geojson->features = $anchorsResource;

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
