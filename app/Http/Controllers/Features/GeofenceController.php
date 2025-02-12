<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\GeofenceCategory;
use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\GeofenceResource;
use App\Models\Features\Geofence;
use App\Rules\MultiPolygonCoordinateRule;
use App\Rules\PolygonCoordinateRule;
use App\Rules\ValidateDisplayPoint;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateIso639;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeofenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $levels = Level::with('feature', 'restriction', 'category', 'labels')->get();
            $geofences = Geofence::get();
            $geofencesResource = GeofenceResource::collection($geofences);

            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $geofencesResource;

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
                'feature_type' => 'required|string|in:geofence',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => 'required',
                'properties.category' => 'required|string|in:' . GeofenceCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),

                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                // 'properties.correlation_id' => 'nullable|uuid|exists:' . TablesName::GEOFENCES . ',geofence_id',
                'properties.correlation_id' => 'nullable|uuid',

                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null','in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.building_ids' => 'nullable|array',
                'properties.building_ids.*' => 'required_if:properties.building_ids,!=null|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
                'properties.level_ids' => 'nullable|array',
                'properties.level_ids.*' => 'required_if:properties.level_ids,!=null|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.parents' => ['nullable', 'array', 'exists:' . TablesName::GEOFENCES . ',geofence_id'],
                'properties.parents.*' => 'required_if:properties.parents,!=null|uuid|exists:' . TablesName::GEOFENCES . ',geofence_id',
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


            $geofence = Geofence::create([
                'geofence_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geofence_category_id' => DB::table(TablesName::GEOFENCE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);


            // add buildings
            if (isset($request->properties['building_ids'])) {

                collect($request->properties['building_ids'])->map(function ($item) use ($geofence) {
                    DB::table(TablesName::GEOFENCE_BUILDING)->insert([
                        'geofence_id' => $geofence->geofence_id,
                        'building_id' => $item
                    ]);
                });
            }

            // add levels
            if (isset($request->properties['level_ids'])) {
                collect($request->properties['level_ids'])->map(function ($item) use ($geofence) {
                    DB::table(TablesName::GEOFENCE_LEVEL)->insert([
                        'geofence_id' => $geofence->geofence_id,
                        'level_id' => $item
                    ]);
                });
            }

            // add parents
            FeatureService::AddFeatureParents(
                $request->properties['parents'] ?? null,
                TablesName::GEOFENCE_PARENTS,
                $geofence->geofence_id,
                'geofence_id',
                'parent_geofence_id'
            );

            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'geofence_id',
                TablesName::GEOFENCE_LABELS,
                $geofence->geofence_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"],
                'alt_name',
                'geofence_id',
                TablesName::GEOFENCE_LABELS,
                $geofence->geofence_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $geofenceResource = GeofenceResource::collection([$geofence]);
        return response()->json(['success' => true, 'data' => $geofenceResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($geofence_id)
    {
        try {
            $geofence = Geofence::query()
                ->where('geofence_id', '=', $geofence_id)->first();

            if (!$geofence)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $geofenceResource = geofenceResource::collection([$geofence]);

            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $geofenceResource;

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
    public function update(Request $request, $geofence_id)
    {
        try {
            // check if the address feature exists
            $geofence = Geofence::query()
                ->where('geofence_id', '=', $geofence_id)->first();
            if (!$geofence)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:' . $geofence_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:geofence',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => 'required',
                'properties.category' => 'required|string|in:' . GeofenceCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),

                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                // 'properties.correlation_id' => 'nullable|uuid|exists:' . TablesName::GEOFENCES . ',geofence_id',
                'properties.correlation_id' => 'nullable|uuid',

                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null','in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.building_ids' => 'nullable|array',
                'properties.building_ids.*' => 'required_if:properties.building_ids,!=null|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
                'properties.level_ids' => 'nullable|array',
                'properties.level_ids.*' => 'required_if:properties.level_ids,!=null|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.parents' => ['nullable', 'array', 'exists:' . TablesName::GEOFENCES . ',geofence_id'],
                'properties.parents.*' => 'required_if:properties.parents,!=null|uuid|exists:' . TablesName::GEOFENCES . ',geofence_id',
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


            $geofence->update([
                'geofence_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geofence_category_id' => DB::table(TablesName::GEOFENCE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);


            // add buildings
            $record = DB::table(TablesName::GEOFENCE_BUILDING)
                ->where('geofence_id', $geofence->geofence_id)
                ->delete();

            if (isset($request->properties['building_ids'])) {
                collect($request->properties['building_ids'])->map(function ($item) use ($geofence) {
                    DB::table(TablesName::GEOFENCE_BUILDING)->insert([
                        'geofence_id' => $geofence->geofence_id,
                        'building_id' => $item
                    ]);
                });
            }

            // add levels
            $record = DB::table(TablesName::GEOFENCE_LEVEL)
                ->where('geofence_id', $geofence->geofence_id)
                ->delete();

            if (isset($request->properties['level_ids'])) {
                collect($request->properties['level_ids'])->map(function ($item) use ($geofence) {
                    DB::table(TablesName::GEOFENCE_LEVEL)->insert([
                        'geofence_id' => $geofence->geofence_id,
                        'level_id' => $item
                    ]);
                });
            }

            // add parents
            FeatureService::UpdateFeatureParents(
                $request->properties['parents'] ?? null,
                TablesName::GEOFENCE_PARENTS,
                $geofence->geofence_id,
                'geofence_id',
                'parent_geofence_id'
            );

            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'geofence_id',
                TablesName::GEOFENCE_LABELS,
                $geofence->geofence_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'geofence_id',
                TablesName::GEOFENCE_LABELS,
                $geofence->geofence_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $geofenceResource = GeofenceResource::collection([$geofence]);
        return response()->json(['success' => true, 'data' => $geofenceResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($geofence_id)
    {
        try {

            $geofence = Geofence::query()
                ->where('geofence_id', '=', $geofence_id)->first();

            if (!$geofence)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $geofence->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
