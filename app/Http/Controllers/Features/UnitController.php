<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\Category\UnitCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\UnitResource;
use App\Models\Features\Feature;
use App\Models\Features\Unit;
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

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $units = Unit::get();
            $unitsResource = UnitResource::collection($units);
            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $unitsResource;

            

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
                'feature_type' => 'required|string|in:unit',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => 'required',
                'properties.category' => 'required|string|in:' . UnitCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name.*' => 'required',
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',

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

            $unit = Unit::create([
                'unit_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'unit_category_id' => DB::table(TablesName::UNIT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint)
            ]);


            // add unit accessibility
            collect($request->properties['accessibility'])->map(function ($item) use ($unit) {
                $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                DB::table(TablesName::UNIT_ACCESSIBILITY)->insert([
                    'unit_id' => $unit->unit_id,
                    'accessibility_id' => $accessibility_id
                ]);
            });


            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'unit_id',
                TablesName::UNIT_LABELS,
                $unit->unit_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'unit_id',
                TablesName::UNIT_LABELS,
                $unit->unit_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $unitResource = UnitResource::collection([$unit]);
        return response()->json(['success' => true, 'data' => $unitResource], 201);
    }
        
    

    /**
     * Display the specified resource.
     */
    public function show($unit_id)
    {
        try {
            $unit = Unit::query()
                ->where('unit_id', '=', $unit_id)->first();

            if (!$unit)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $unitsResource = UnitResource::collection([$unit]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $unitsResource;

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
    public function update(Request $request, $unit_id)
    {
        try {
            // check if the address feature exists
            $unit = Unit::query()
                ->where('unit_id', '=', $unit_id)->first();
            if (!$unit)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:' . $unit_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:unit',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => 'required',
                'properties.category' => 'required|string|in:' . UnitCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',

            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database
            // convert geojson to WKT format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            $textPolygon = Geom::GeomFromText($request->geometry);
            // convert geojson to WKT format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"] ?? null);

            // Start the transaction
            DB::beginTransaction();

            $unit->update([
                'unit_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'unit_category_id' => DB::table(TablesName::UNIT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint)
            ]);


            // add unit accessibility

            $record = DB::table(TablesName::UNIT_ACCESSIBILITY)
                ->where('unit_id', $unit->unit_id)
                ->delete();

            collect($request->properties['accessibility'])->map(function ($item) use ($unit) {
                $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                DB::table(TablesName::UNIT_ACCESSIBILITY)->insert([
                    'unit_id' => $unit->unit_id,
                    'accessibility_id' => $accessibility_id
                ]);
            });


            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'unit_id',
                TablesName::UNIT_LABELS,
                $unit->unit_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'unit_id',
                TablesName::UNIT_LABELS,
                $unit->unit_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $unitResource = UnitResource::collection([$unit]);
        return response()->json(['success' => true, 'data' => $unitResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($unit_id)
    {
        try {

            $unit = Unit::query()
                ->where('unit_id', '=', $unit_id)->first();

            if (!$unit)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $unit->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
