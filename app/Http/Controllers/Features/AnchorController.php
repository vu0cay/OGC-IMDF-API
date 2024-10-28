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
        $anchors = Anchor::with('feature')->get();
        $anchorsResource = AnchorResource::collection($anchors);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $anchorsResource;

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
