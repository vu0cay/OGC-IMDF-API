<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\FootprintCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\FootprintResource;
use App\Models\Features\Footprint;
use App\Rules\MultiPolygonCoordinateRule;
use App\Rules\PolygonCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FootprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $footprints = Footprint::get();
            $footprintsResource = FootprintResource::collection($footprints);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $footprintsResource;
    
            return response()->json([$geojson], 200);
        }
        catch (Exception $e) {
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
        try {
            // validation
            $attributes = Validator::make($request->all(), [
                // 'id' => 'required|uuid|unique:' . TablesName::FOOTPRINTS . ',footprint_id',
                'id' => ['required','uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:footprint',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => ['required', function($attribute, $value, $fail) use($request) {
                    if($request->geometry['type'] === 'Polygon') {
                        $validateInstance = new PolygonCoordinateRule();
                        $validateInstance->validate($attribute, $value, $fail);
                    } else {
                        $validateInstance = new MultiPolygonCoordinateRule();
                        $validateInstance->validate($attribute, $value, $fail);
                    }

                }],
                'properties.category' => 'required|string|in:' . FootprintCategory::getConstansAsString(),
                'properties.name' => 'nullable|array',
                'properties.building_ids' => 'required|array',
                'properties.building_ids.*' => 'required|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }



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
            collect($request->properties['building_ids'])->map(function ($item) use ($footprint) {
                DB::table(TablesName::FOOTPRINT_BUILDING)->insert([
                    'footprint_id' => $footprint->footprint_id,
                    'building_id' => $item
                ]);
            });

            // add labels
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'footprint_id',
                TablesName::FOOTPRINT_LABELS,
                $footprint->footprint_id
            );

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
        // change to IMDF json format 
        $footprintResource = FootprintResource::collection([$footprint]);
        // return response 
        return response()->json(['success' => true, 'data' => $footprintResource], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($footprint_id)
    {
        try{
            $footprints = Footprint::query()
                ->where('footprint_id', '=', $footprint_id)
                ->first();
            if (!$footprints)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
    
    
            $footprintsResource = FootprintResource::collection([$footprints]);
    
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $footprintsResource;
    
            return response()->json([$geojson], 200);
        }
        catch (Exception $e) {
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
    public function update(Request $request, $footprint_id)
    {
        try {
            // check if the address feature exists
            $footprint = Footprint::query()
                ->where('footprint_id', '=', $footprint_id)->first();
            if (!$footprint)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => 'required|uuid|in:' . $footprint_id,
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:footprint',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon',
                'geometry.coordinates' => ['required', new PolygonCoordinateRule],
                'properties.category' => 'required|string|in:' . FootprintCategory::getConstansAsString(),
                'properties.name' => 'nullable|array',
                'properties.building_ids' => 'required|array',
                'properties.building_ids.*' => 'required|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // add feature here ....
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);

            // Start the transaction
            // DB::beginTransaction();
            $footprint->update([
                'footprint_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'footprint_category_id' => DB::table(TablesName::FOOTPRINT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
            ]);
            // $footprint->buildings()->delete();
            
            $record = DB::table(TablesName::FOOTPRINT_BUILDING . ' as fb')
                ->where('footprint_id', $footprint->footprint_id)
                ->delete();

            // add buildings    
            collect($request->properties['building_ids'])->map(function ($item) use ($footprint) {
                DB::table(TablesName::FOOTPRINT_BUILDING)->insert([
                    'footprint_id' => $footprint->footprint_id,
                    'building_id' => $item
                ]);
            });

            // add labels
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'footprint_id',
                TablesName::FOOTPRINT_LABELS,
                $footprint->footprint_id
            );
            // DB::commit();
        } catch (Exception $e) {

            // DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
        // change to IMDF json format 
        $footprintResource = FootprintResource::collection([$footprint]);
        // return response 
        return response()->json(['success' => true, 'data' => $footprintResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($footprint_id)
    {
        try{
            $footprint = Footprint::query()
            ->where('footprint_id', '=', $footprint_id)->first();
    
            if (!$footprint)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
    
            // $footprint->address()->delete();
            // $footprint->labels()->delete();
            
            $footprint->delete();
            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
