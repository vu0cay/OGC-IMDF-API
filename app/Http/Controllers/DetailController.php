<?php

namespace App\Http\Controllers;

use App\Constants\Features\TablesName;
use App\Contracts\Geom;
use App\Http\Resources\FeatureResources\DetailResource;
use App\Models\Features\Detail;
use App\Rules\LineStringCoordinateRule;
use App\Rules\MultiLineStringCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            $details = Detail::get();
            $detailsResource = DetailResource::collection($details);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $detailsResource;


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
        try {
            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:detail',
                'geometry' => 'required',
                'geometry.type' => 'required|in:LineString,MultiLineString',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if(!isset($request->geometry['type'])) return;

                        if ($request->geometry['type'] === 'LineString') {
                            $validateInstance = new LineStringCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiLineStringCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);

                        }

                    }
                ],
                'properties.level_id' => 'required|exists:' . TablesName::LEVELS . ',level_id',

            ]);
            
            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }
            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);
            // dd($textPolygon);
            

            // Start the transaction
            DB::beginTransaction();

            $detail = Detail::create([
                'detail_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
            ]);

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $detailResource = DetailResource::collection([$detail]);
        return response()->json(['success' => true, 'data' => $detailResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($detail_id)
    {
        try {
            $detail = Detail::query()
                ->where('detail_id', '=', $detail_id)->first();

            if (!$detail)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $detailsResource = DetailResource::collection([$detail]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $detailsResource;

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
    public function update(Request $request, $detail_id)
    {
        try {
            $detail = Detail::query()
                ->where('detail_id', '=', $detail_id)->first();

            if (!$detail)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$detail_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:detail',
                'geometry' => 'required',
                'geometry.type' => 'required|in:LineString,MultiLineString',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if(!isset($request->geometry['type'])) return;

                        if ($request->geometry['type'] === 'LineString') {
                            $validateInstance = new LineStringCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiLineStringCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);

                        }

                    }
                ],
                'properties.level_id' => 'required|exists:' . TablesName::LEVELS . ',level_id',

            ]);
            
            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }
            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);
            // dd($textPolygon);
            
            // Start the transaction
            DB::beginTransaction();

            $detail->update([
                'detail_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
            ]);

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $detailResource = DetailResource::collection([$detail]);
        return response()->json(['success' => true, 'data' => $detailResource], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $detail_id)
    {
        try {

            $detail = Detail::query()
                ->where('detail_id', '=', $detail_id)->first();

            if (!$detail)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $detail->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
