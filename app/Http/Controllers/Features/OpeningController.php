<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\OpeningCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\OpeningResource;
use App\Models\Features\Opening;
use App\Models\FeaturesCategory\Door;
use App\Rules\LineStringCoordinateRule;
use App\Rules\Opening\ValidateDoor;
use App\Rules\PointCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateIso639;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpeningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            $openings = Opening::get();
            $openingsResource = OpeningResource::collection($openings);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $openingsResource;

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
                'feature_type' => 'required|string|in:opening',

                'geometry' => 'required',
                'geometry.type' => 'required|in:LineString',
                'geometry.coordinates' => [
                    'required',
                    new LineStringCoordinateRule
                ],
                'properties.category' => 'required|string|in:' . OpeningCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.access_control' => 'nullable|array',
                'properties.access_control.*' => 'required_if:properties.access_control,!=null|exists:' . TablesName::ACCESSCONTROLS . ',name',
                'properties.door' => ['nullable', new ValidateDoor],
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
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

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

            // Start the transaction
            DB::beginTransaction();
            $door_id = isset($request->properties["door"]) ?
                Door::create([
                    "automatic" => $request->properties["door"]["automatic"],
                    "material" => $request->properties["door"]["material"],
                    "type" => $request->properties["door"]["type"],
                ])->id : null;


            $opening = Opening::create([
                'opening_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'opening_category_id' => DB::table(TablesName::OPENING_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint),
                "door_id" => $door_id
            ]);

            // add unit accessibility
            if (isset($request->properties['accessibility'])) {

                collect($request->properties['accessibility'])->map(function ($item) use ($opening) {
                    $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                    DB::table(TablesName::OPENING_ACCESSIBILITY)->insert([
                        'opening_id' => $opening->opening_id,
                        'accessibility_id' => $accessibility_id
                    ]);
                });
            }

            // add unit access_control
            if (isset($request->properties['access_control'])) {
                collect($request->properties['access_control'])->map(function ($item) use ($opening) {
                    $access_control_id = DB::table(TablesName::ACCESSCONTROLS)->where('name', $item)->first()->id;
                    DB::table(TablesName::OPENING_ACCESSCONTROL)->insert([
                        'opening_id' => $opening->opening_id,
                        'access_control_id' => $access_control_id
                    ]);
                });
            }

            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'opening_id',
                TablesName::OPENING_LABELS,
                $opening->opening_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'opening_id',
                TablesName::OPENING_LABELS,
                $opening->opening_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $openingResource = OpeningResource::collection([$opening]);
        return response()->json(['success' => true, 'data' => $openingResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($opening_id)
    {
        try {
            $opening = Opening::query()
                ->where('opening_id', '=', $opening_id)->first();

            if (!$opening)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $openingsResource = OpeningResource::collection([$opening]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $openingsResource;

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
    public function update(Request $request, $opening_id)
    {
        try {

            // check if the opening feature exists
            $opening = Opening::query()
                ->where('opening_id', '=', $opening_id)->first();
            if (!$opening)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:' . $opening_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:opening',

                'geometry' => 'required',
                'geometry.type' => 'required|in:LineString',
                'geometry.coordinates' => [
                    'required',
                    new LineStringCoordinateRule
                ],
                'properties.category' => 'required|string|in:' . OpeningCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',

                'properties.access_control' => 'nullable|array',
                'properties.access_control.*' => 'required_if:properties.access_control,!=null|exists:' . TablesName::ACCESSCONTROLS . ',name',

                'properties.door' => ['nullable', new ValidateDoor],

                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
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

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

            // Start the transaction
            DB::beginTransaction();


            $door_id = isset($request->properties["door"]) ?
                Door::create([
                    "automatic" => $request->properties["door"]["automatic"],
                    "material" => $request->properties["door"]["material"],
                    "type" => $request->properties["door"]["type"],
                ])->id : null;


            $opening->update([
                'opening_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'opening_category_id' => DB::table(TablesName::OPENING_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint),
                "door_id" => $door_id
            ]);

            // add opening accessibility
            $record = DB::table(TablesName::OPENING_ACCESSIBILITY)
                ->where('opening_id', $opening->opening_id)
                ->delete();

            if (isset($request->properties['accessibility'])) {
                collect($request->properties['accessibility'])->map(function ($item) use ($opening) {
                    $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                    DB::table(TablesName::OPENING_ACCESSIBILITY)->insert([
                        'opening_id' => $opening->opening_id,
                        'accessibility_id' => $accessibility_id
                    ]);
                });
            }

            // add unit access_control
            $record = DB::table(TablesName::OPENING_ACCESSCONTROL)
                ->where('opening_id', $opening->opening_id)
                ->delete();

            if (isset($request->properties['access_control'])) {
                collect($request->properties['access_control'])->map(function ($item) use ($opening) {
                    $access_control_id = DB::table(TablesName::ACCESSCONTROLS)->where('name', $item)->first()->id;
                    DB::table(TablesName::OPENING_ACCESSCONTROL)->insert([
                        'opening_id' => $opening->opening_id,
                        'access_control_id' => $access_control_id
                    ]);
                });
            }


            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'opening_id',
                TablesName::OPENING_LABELS,
                $opening->opening_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'opening_id',
                TablesName::OPENING_LABELS,
                $opening->opening_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $openingResource = OpeningResource::collection([$opening]);
        return response()->json(['success' => true, 'data' => $openingResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($opening_id)
    {
        try {

            $opening = Opening::query()
                ->where('opening_id', '=', $opening_id)->first();

            if (!$opening)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $opening->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
