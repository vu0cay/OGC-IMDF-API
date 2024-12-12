<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\KioskResource;
use App\Models\Features\Kiosk;
use Exception;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $kiosks = Kiosk::get();
            $kiosksResource = KioskResource::collection($kiosks);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $kiosksResource;

            // return $feature;
            return response()->json($geojson, 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
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
    public function show($kiosk_id)
    {
        try {
            $kiosk = Kiosk::query()
                ->where('kiosk_id', '=', $kiosk_id)->first();

            if (!$kiosk)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $kiosksResource = KioskResource::collection([$kiosk]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $kiosksResource;

            return response()->json($geojson, 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
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
