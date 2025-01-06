<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\FixtureCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\FixtureResource;
use App\Models\Features\Fixture;
use App\Rules\MultiPolygonCoordinateRule;
use App\Rules\PointCoordinateRule;
use App\Rules\PolygonCoordinateRule;
use App\Rules\ValidateDisplayPoint;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateIso639;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FixtureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            $fixtures = Fixture::get();

            $fixturesResource = FixtureResource::collection($fixtures);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $fixturesResource;

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
                'feature_type' => 'required|string|in:fixture',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!isset($request->geometry['type']))
                            return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.category' => 'required|string|in:' . FixtureCategory::getConstansAsString(),
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.anchor_id' => 'nullable|uuid|exists:' . TablesName::ANCHORS . ',anchor_id',

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
            $txtPoint = Geom::GeomFromText($request->properties["display_point"] ?? null);

            // Start the transaction
            DB::beginTransaction();
            // DB::table(TablesName::FIXTURES)->insert([
            //     "fixture_id" => "12345678-8888-8888-8888-888888888888",
            //     "feature_id" => 6,
            //     "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            //     "fixture_category_id" => 5,
            //     "display_point" => "POINT(100.0 1.0)",
            //     "anchor_id" => "99999999-9999-9999-9999-999999999999",
            //     "level_id" => "77777777-7777-7777-7777-777777777777"
            // ]);

            $fixture = Fixture::create([
                'fixture_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'fixture_category_id' => DB::table(TablesName::FIXTURE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'anchor_id' => $request->properties["anchor_id"] ?? null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);



            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'fixture_id',
                TablesName::FIXTURE_LABELS,
                $fixture->fixture_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'fixture_id',
                TablesName::FIXTURE_LABELS,
                $fixture->fixture_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $fixtureResource = FixtureResource::collection([$fixture]);
        return response()->json(['success' => true, 'data' => $fixtureResource], 201);
    }
    // public function store(Request $requests)
    // {
       
    //         foreach ($requests->features as $request) {
    //             try {
    //         // validation
    //         $attributes = Validator::make($request, [
    //             'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
    //             'type' => 'in:Feature',
    //             'feature_type' => 'required|string|in:fixture',
    //             'geometry' => 'required',
    //             'geometry.type' => 'required|in:Polygon,MultiPolygon',
    //             'geometry.coordinates' => [
    //                 'required',
    //                 function ($attribute, $value, $fail) use ($request) {
    //                     if (!isset($request['geometry']['type']))
    //                         return;

    //                     if ($request['geometry']['type'] === 'Polygon') {
    //                         $validateInstance = new PolygonCoordinateRule();
    //                         $validateInstance->validate($attribute, $value, $fail);
    //                     } else {
    //                         $validateInstance = new MultiPolygonCoordinateRule();
    //                         $validateInstance->validate($attribute, $value, $fail);
    //                     }

    //                 }
    //             ],
    //             'properties.category' => 'required|string|in:' . FixtureCategory::getConstansAsString(),
    //             'properties.name' => ['nullable', 'array', new ValidateIso639],
    //             // 'properties.name.*' => 'required',
    //             'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
    //             // 'properties.alt_name.*' => 'required',
    //             'properties.display_point' => ['nullable', new ValidateDisplayPoint],
    //             // 'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
    //             // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
    //             'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',
    //             'properties.anchor_id' => 'nullable|uuid|exists:' . TablesName::ANCHORS . ',anchor_id',

    //         ]);

    //         // Bad Request
    //         if ($attributes->fails()) {
    //             $error = $attributes->errors()->first();
    //             return response()->json(['success' => false, 'message' => $error], 400);
    //         }

    //         // Adding feature to the database
    //         // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
    //         $textPolygon = Geom::GeomFromText($request['geometry']);

    //         // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
    //         $txtPoint = Geom::GeomFromText($request['properties']["display_point"] ?? null);

    //         // Start the transaction
    //         DB::beginTransaction();
    //         // DB::table(TablesName::FIXTURES)->insert([
    //         //     "fixture_id" => "12345678-8888-8888-8888-888888888888",
    //         //     "feature_id" => 6,
    //         //     "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
    //         //     "fixture_category_id" => 5,
    //         //     "display_point" => "POINT(100.0 1.0)",
    //         //     "anchor_id" => "99999999-9999-9999-9999-999999999999",
    //         //     "level_id" => "77777777-7777-7777-7777-777777777777"
    //         // ]);

    //         $fixture = Fixture::create([
    //             'fixture_id' => $request['id'],
    //             'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request['feature_type'])->first()->id,
    //             'fixture_category_id' => DB::table(TablesName::FIXTURE_CATEGORIES)->where("name", $request['properties']['category'])->first()->id,
    //             'geometry' => DB::raw($textPolygon),
    //             'level_id' => $request['properties']["level_id"],
    //             'anchor_id' => $request['properties']["anchor_id"] ?? null,
    //             'display_point' => DB::raw(value: $txtPoint)
    //         ]);



    //         // label name
    //         FeatureService::AddFeatureLabel(
    //             $request['properties']["name"] ?? null,
    //             'name',
    //             'fixture_id',
    //             TablesName::FIXTURE_LABELS,
    //             $fixture['fixture_id']
    //         );
    //         // label short_name
    //         FeatureService::AddFeatureLabel(
    //             $request['properties']["alt_name"] ?? null,
    //             'alt_name',
    //             'fixture_id',
    //             TablesName::FIXTURE_LABELS,
    //             $fixture['fixture_id']
    //         );

    //         // Commit the transaction
    //         DB::commit();

    //     } catch (Exception $e) {
    
    //         // Roll back the transaction if there's an error occur.
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
    //     }
    //     }
    //     // $fixtureResource = FixtureResource::collection([$fixture]);
    //     return response()->json(['success' => true], 201);
    // }

    /**
     * Display the specified resource.
     */
    public function show($fixture_id)
    {
        try {
            $fixture = Fixture::query()
                ->where('fixture_id', '=', $fixture_id)->first();

            if (!$fixture)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $fixturesResource = FixtureResource::collection([$fixture]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $fixturesResource;

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
    public function update(Request $request, $fixture_id)
    {
        try {
            $fixture = Fixture::query()
                ->where('fixture_id', '=', $fixture_id)->first();
            if (!$fixture)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:' . $fixture_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:fixture',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!isset($request->geometry['type']))
                            return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.category' => 'required|string|in:' . FixtureCategory::getConstansAsString(),
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.anchor_id' => 'nullable|uuid|exists:' . TablesName::ANCHORS . ',anchor_id',
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
            $txtPoint = Geom::GeomFromText($request->properties["display_point"] ?? null);

            // Start the transaction
            DB::beginTransaction();

            $fixture->update([
                'fixture_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'fixture_category_id' => DB::table(TablesName::FIXTURE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'anchor_id' => $request->properties["anchor_id"] ?? null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);



            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'fixture_id',
                TablesName::FIXTURE_LABELS,
                $fixture->fixture_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'fixture_id',
                TablesName::FIXTURE_LABELS,
                $fixture->fixture_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $fixtureResource = FixtureResource::collection([$fixture]);
        return response()->json(['success' => true, 'data' => $fixtureResource], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($fixture_id)
    {
        try {

            $fixture = Fixture::query()
                ->where('fixture_id', '=', $fixture_id)->first();

            if (!$fixture)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $fixture->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
