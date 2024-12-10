<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\LevelCategory;
use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\LevelResource;
use App\Models\Features\Level;
use App\Rules\PointCoordinateRule;
use App\Rules\PolygonCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            // $levels = Level::with('feature', 'restriction', 'category', 'labels')->get();
            $levels = Level::get();
            $levelsResource = LevelResource::collection($levels);
            
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $levelsResource;
            
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
                'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:level',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon',
                'geometry.coordinates' => ['required', new PolygonCoordinateRule],
                'properties.category' => 'required|string|in:' . LevelCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.name' => ['required', 'array'],
                'properties.name.*' => 'required',
                'properties.short_name' => ['required', 'array'],
                'properties.short_name.*' => 'required',
                'properties.outdoor' => ['required','boolean'],
                'properties.ordinal' => ['required','integer','min:0'],
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null','in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.building_ids' => 'nullable|array',
                'properties.building_ids.*' => 'required_if:properties.building_ids,!=null|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
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
            $level = Level::create([
                'level_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'level_category_id' => DB::table(TablesName::LEVEL_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'outdoor' => $request->properties['outdoor'],
                'ordinal' => $request->properties['ordinal'],
                'display_point' => DB::raw(value: $txtPoint)
            ]);

            // add level address
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_LEVELS)->insert([
                    'level_id' => $level->level_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // add buildings
            collect($request->properties['building_ids'])->map(function ($item) use ($level) {
                DB::table(TablesName::LEVEL_BUILDING)->insert([
                    'level_id' => $level->level_id,
                    'building_id' => $item
                ]);
            });

            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'level_id',
                TablesName::LEVEL_LABELS,
                $level->level_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["short_name"],
                'short_name',
                'level_id',
                TablesName::LEVEL_LABELS,
                $level->level_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $levelResource = LevelResource::collection([$level]);
        return response()->json(['success' => true, 'data' => $levelResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($level_id)
    {
        try{
            $level = Level::query()
                ->where('level_id', '=', $level_id)->first();
            
            if (!$level) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);
    
            $levelResource = LevelResource::collection([$level]);
    
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $levelResource;
    
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
    public function update(Request $request, $level_id)
    {
        try {
            // check if the address feature exists
            $level = Level::query()
                ->where('level_id', '=', $level_id)->first();
            if (!$level)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$level_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:level',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon',
                'geometry.coordinates' => ['required', new PolygonCoordinateRule],
                'properties.category' => 'required|string|in:' . LevelCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.name' => ['required', 'array'],
                'properties.name.*' => 'required',
                'properties.short_name' => ['required', 'array'],
                'properties.short_name.*' => 'required',
                'properties.outdoor' => ['required','boolean'],
                'properties.ordinal' => ['required','integer','min:0'],
                'properties.display_point' => 'nullable',
                'properties.display_point.type' => ['required_if:properties.display_point,!=null','in:Point'],
                'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.building_ids' => 'nullable|array',
                'properties.building_ids.*' => 'required_if:properties.building_ids,!=null|uuid|exists:' . TablesName::BUILDINGS . ',building_id',
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
            $level->update([
                'level_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'level_category_id' => DB::table(TablesName::LEVEL_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'outdoor' => $request->properties['outdoor'],
                'ordinal' => $request->properties['ordinal'],
                'display_point' => DB::raw(value: $txtPoint)
            ]);

            // add level address
            $record = DB::table(TablesName::ADDRESS_LEVELS . ' as address_feature')
                ->where('level_id', $level->level_id)
                ->delete();

            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_LEVELS)->insert([
                    'level_id' => $level->level_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // update buildings
            $record = DB::table(TablesName::LEVEL_BUILDING)
                ->where('level_id', $level->level_id)
                ->delete();

            collect($request->properties['building_ids'])->map(function ($item) use ($level) {
                DB::table(TablesName::LEVEL_BUILDING)->insert([
                    'level_id' => $level->level_id,
                    'building_id' => $item
                ]);
            });

            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"],
                'name',
                'level_id',
                TablesName::LEVEL_LABELS,
                $level->level_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["short_name"],
                'short_name',
                'level_id',
                TablesName::LEVEL_LABELS,
                $level->level_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $levelResource = LevelResource::collection([$level]);
        return response()->json(['success' => true, 'data' => $levelResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($level_id)
    {
        try{

            $level = Level::query()
            ->where('level_id', '=', $level_id)->first();
    
            if (!$level)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
            
            $level->delete();
    
            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
