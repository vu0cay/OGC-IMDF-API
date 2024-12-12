<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\KioskResource;
use App\Models\Features\Kiosk;
use App\Rules\MultiPolygonCoordinateRule;
use App\Rules\PointCoordinateRule;
use App\Rules\PolygonCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateIso639;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        try {
            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:kiosk',
                'geometry' => 'required',
                'geometry.type' => ['required','in:Polygon,MultiPolygon'],
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if(!isset($request->geometry['type'])) return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PolygonCoordinateRule],
                'properties.level_id' => 'required|exists:' . TablesName::LEVELS . ',level_id',
                'properties.anchor_id' => 'nullable|exists:' . TablesName::ANCHORS . ',anchor_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

            // Start the transaction
            DB::beginTransaction();


            $kiosk = Kiosk::create([
                'kiosk_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'anchor_id' => $request->properties["anchor_id"] ?? null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);


            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'kiosk_id',
                TablesName::KIOSK_LABELS,
                $kiosk->kiosk_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'kiosk_id',
                TablesName::KIOSK_LABELS,
                $kiosk->kiosk_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $kioskResource = KioskResource::collection([$kiosk]);
        return response()->json(['success' => true, 'data' => $kioskResource], 201);
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
    public function update(Request $request, $kiosk_id)
    {
        try {

            // check if the address feature exists
            $kiosk = Kiosk::query()
                ->where('kiosk_id', '=', $kiosk_id)->first();
            if (!$kiosk)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$kiosk_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:kiosk',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if(!isset($request->geometry['type'])) return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.level_id' => 'required|exists:' . TablesName::LEVELS . ',level_id',
                'properties.anchor_id' => 'nullable|exists:' . TablesName::ANCHORS . ',anchor_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

            // Start the transaction
            DB::beginTransaction();


            $kiosk->update([
                'kiosk_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'anchor_id' => $request->properties["anchor_id"],
                'display_point' => DB::raw(value: $txtPoint)
            ]);



            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'kiosk_id',
                TablesName::KIOSK_LABELS,
                $kiosk->kiosk_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'kiosk_id',
                TablesName::KIOSK_LABELS,
                $kiosk->kiosk_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $kioskResource = KioskResource::collection([$kiosk]);
        return response()->json(['success' => true, 'data' => $kioskResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kiosk_id)
    {
        try {

            $kiosk = Kiosk::query()
                ->where('kiosk_id', '=', $kiosk_id)->first();

            if (!$kiosk)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $kiosk->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
