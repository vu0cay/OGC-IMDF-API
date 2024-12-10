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
        // $buildings = Building::with('feature', 'restriction', 'category')->get();
        $buildings = Building::get();
        $buildingsResource = BuildingResource::collection($buildings);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;
        
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
        // validation

        $attributes = Validator::make($request->all(), [
            'id' => 'required|uuid|unique:' . TablesName::FOOTPRINTS . ',footprint_id',
            'type' => 'in:Feature',
            'feature_type' => 'required|string|in:footprint',
            'geometry' => 'required',
            'geometry.type' => 'required|in:Polygon',
            'geometry.coordinates' => ['required', new PolygonCoordinateRule],
            'properties.category' => 'required|string|in:' . FootprintCategory::getConstansAsString(),
            'properties.name' => 'nullable|array',
            'properties.building_ids' => 'required|array',
        ]);

        // Bad Request
        if ($attributes->fails()) {
            $error = $attributes->errors()->first();
            return response()->json(['success' => false, 'message' => $error], 400);
        }


        try{
            
            // add feature here ....
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);

            // Start the transaction
            DB::beginTransaction();
            $footprint = Footprint::create([
                'footprint_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'footprint_category_id' => DB::table(TablesName::FOOTPRINT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
            ]);

            // add buildings

            // add labels

            DB::commit();
        } catch(Exception $e) {
            
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($building_id)
    {
        $buildings = Building::query()
                            ->where('building_id', '=', $building_id)
                            // ->with('feature', 'restriction', 'category')
                            ->first();
                            
        if (!$buildings) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);

        $buildingsResource = BuildingResource::collection([$buildings]);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;
        
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
