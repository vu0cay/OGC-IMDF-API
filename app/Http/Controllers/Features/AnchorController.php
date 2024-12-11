<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\TablesName;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AnchorResource;
use App\Http\Resources\FeatureResources\UnitResource;
use App\Models\Features\Anchor;
use App\Models\Features\Unit;
use App\Rules\PointCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnchorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            // $anchors = Anchor::with('feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            // $anchors = Anchor::with('feature')->get();
            $anchors = Anchor::get();
            $anchorsResource = AnchorResource::collection($anchors);
            
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $anchorsResource;
    
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
                'feature_type' => 'required|string|in:anchor',
                'geometry' => 'required',
                'geometry.type' => ['required','in:Point'],
                'geometry.coordinates' => ['required', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.unit_id' => 'required|exists:' . TablesName::UNITS . ',unit_id',
                
            ]);
            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // adding feature to the database 

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->geometry);

            $relatedUnit = Unit::where('unit_id', $request->properties["unit_id"])->first();
            $level = $relatedUnit->level->pluck('level_id')->toArray();

            DB::beginTransaction();
            $anchor = Anchor::create([
                'anchor_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw(value: $txtPoint),
                'unit_id' => $request->properties["unit_id"],
                'level_id' => $level[0]
            ]);
            // add level address
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_ANCHORS)->insert([
                    'anchor_id' => $anchor->anchor_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // Commit the transaction
            DB::commit();
        } catch (Exception $e) {
            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
        
        $anchorResource = AnchorResource::collection([$anchor]);
        // return response 
        return response()->json(['success' => true, 'data' => $anchorResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($anchor_id)
    {
        try{
            $anchor = Anchor::query()
                        ->where('anchor_id', '=', $anchor_id)->first();
            if (!$anchor) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);
            
            $anchorsResource = AnchorResource::collection([$anchor]);
    
            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
    
            $geojson = json_decode($geojson);
            $geojson->features = $anchorsResource;
    
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
    public function update(Request $request, $anchor_id)
    {
        try {
            // check if the address feature exists
            $anchor = Anchor::query()
                ->where('anchor_id', '=', $anchor_id)->first();
            if (!$anchor)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$anchor_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:anchor',
                'geometry' => 'required',
                'geometry.type' => ['required','in:Point'],
                'geometry.coordinates' => ['required', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.unit_id' => 'required|exists:' . TablesName::UNITS . ',unit_id',
                
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // adding feature to the database 

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->geometry);

            $relatedUnit = Unit::where('unit_id', $request->properties["unit_id"])->first();
            $level = $relatedUnit->level->pluck('level_id')->toArray();

            DB::beginTransaction();
            $anchor->update([
                'anchor_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'geometry' => DB::raw(value: $txtPoint),
                'unit_id' => $request->properties["unit_id"],
                'level_id' => $level[0]
            ]);

             // add level address
             $record = DB::table(TablesName::ADDRESS_ANCHORS . ' as address_feature')
             ->where('anchor_id', $anchor->anchor_id)
             ->delete();

            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_ANCHORS)->insert([
                    'anchor_id' => $anchor->anchor_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // Commit the transaction
            DB::commit();
        } catch (Exception $e) {
            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
        
        $anchorResource = AnchorResource::collection([$anchor]);
        // return response 
        return response()->json(['success' => true, 'data' => $anchorResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $anchor_id)
    {
        try{

            $anchor = Anchor::query()
            ->where('anchor_id', '=', $anchor_id)->first();
    
            if (!$anchor)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
            
            $anchor->delete();
    
            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
