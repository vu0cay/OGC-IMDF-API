<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AddressResource;
use App\Models\Features\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = Address::get();
        $addressesResource = AddressResource::collection($addresses);
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

        //$geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $addressesResource;

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
    public function show($address_id)
    {
        $address = Address::query()
            ->where('address_id', '=', $address_id)->first();

        if (!$address) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);

        $addressesResource = AddressResource::collection([$address]);

        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $addressesResource;

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
