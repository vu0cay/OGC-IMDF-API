<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\OccupantCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\OccupantResource;
use App\Models\Features\Occupant;
use App\Models\FeaturesCategory\Validity;
use App\Rules\Occupant\ValidateValidity;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateHours;
use App\Rules\ValidateIso639;
use App\Rules\Venue\ValidatePhone;
use App\Rules\Venue\ValidateWebsiteUri;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OccupantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            // $venues = Venue::with('feature', 'restriction', 'category', 'labels')->get();
            $occupants = Occupant::get();
            $occupantsResource = OccupantResource::collection($occupants);
    
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $occupantsResource;
    
            return response()->json($geojson, 200);
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
                // 'id' => 'required|uuid|unique:' . TablesName::VENUES . ',venue_id',
                'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:occupant',
                'properties.category' => 'required|string|in:' . OccupantCategory::getConstansAsString(),
                'properties.name' => ['required', 'array',new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.hours' => ['nullable','string',new ValidateHours],
                'properties.website' => ['nullable','string', new ValidateWebsiteUri],
                'properties.phone' => ['nullable','string',new ValidatePhone],
                'properties.anchor_id' => 'nullable|exists:' . TablesName::ANCHORS . ',anchor_id',
                'properties.validity' => ['required', new ValidateValidity]
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // Adding feature to the database

            // Start the transaction
            DB::beginTransaction();

            $validity_id = isset($request->properties["validity"]) ?
                Validity::create([
                    "start" => $request->properties["validity"]["start"],
                    "end" => $request->properties["validity"]["end"],
                    "modified" => $request->properties["validity"]["modified"],
                ])->id : null;

            $occupant = Occupant::create([
                'occupant_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'validity_id' => $validity_id,
                'occupant_category_id' => DB::table(TablesName::OCCUPANT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'hours' => isset($request->properties['hours']) ? $request->properties['hours'] : null,
                'website' => isset($request->properties['website']) ? $request->properties['website'] : null,
                'phone' => isset($request->properties['phone']) ? $request->properties['phone'] : null,
                'anchor_id' => isset($request->properties['anchor_id']) ? $request->properties['anchor_id'] : null,
                'correlation_id' => isset($request->properties['correlation_id']) ? $request->properties['correlation_id'] : null
            ]);


            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'occupant_id',
                TablesName::OCCUPANT_LABELS,
                $occupant->occupant_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $occupantResource = OccupantResource::collection([$occupant]);
        return response()->json(['success' => true, 'data' => $occupantResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($occupant_id)
    {
        try{
            $occupant = Occupant::query()
                // ->with('feature', 'restriction', 'category')
                ->where('occupant_id', '=', $occupant_id)->first();
    
            if (!$occupant)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
    
    
            $occupantsResource = occupantResource::collection([$occupant]);
    
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $occupantsResource;
    
            return response()->json($geojson, 200);
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
    public function update(Request $request, $occupant_id)
    {
        try {
            // check if the opening feature exists
            $occupant = Occupant::query()
                ->where('occupant_id', '=', $occupant_id)->first();
            if (!$occupant)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                // 'id' => 'required|uuid|unique:' . TablesName::VENUES . ',venue_id',
                'id' => ['required', 'uuid', 'in:'.$occupant_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:occupant',
                'properties.category' => 'required|string|in:' . OccupantCategory::getConstansAsString(),
                'properties.name' => ['required', 'array',new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.hours' => ['nullable','string',new ValidateHours],
                'properties.website' => ['nullable','string', new ValidateWebsiteUri],
                'properties.phone' => ['nullable','string',new ValidatePhone],
                'properties.anchor_id' => 'nullable|exists:' . TablesName::ANCHORS . ',anchor_id',
                'properties.validity' => ['required', new ValidateValidity]
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // Adding feature to the database

            // Start the transaction
            DB::beginTransaction();

            $validity_id = isset($request->properties["validity"]) ?
                Validity::create([
                    "start" => $request->properties["validity"]["start"],
                    "end" => $request->properties["validity"]["end"],
                    "modified" => $request->properties["validity"]["modified"],
                ])->id : null;

            $occupant->update([
                'occupant_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'validity_id' => $validity_id,
                'occupant_category_id' => DB::table(TablesName::OCCUPANT_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'hours' => isset($request->properties['hours']) ? $request->properties['hours'] : null,
                'website' => isset($request->properties['website']) ? $request->properties['website'] : null,
                'phone' => isset($request->properties['phone']) ? $request->properties['phone'] : null,
                'anchor_id' => isset($request->properties['anchor_id']) ? $request->properties['anchor_id'] : null,
                'correlation_id' => isset($request->properties['correlation_id']) ? $request->properties['correlation_id'] : null
            ]);


            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"],
                'name',
                'occupant_id',
                TablesName::OCCUPANT_LABELS,
                $occupant->occupant_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $occupantResource = OccupantResource::collection([$occupant]);
        return response()->json(['success' => true, 'data' => $occupantResource], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($occupant_id)
    {
        try {

            $occupant = Occupant::query()
                ->where('occupant_id', '=', $occupant_id)->first();

            if (!$occupant)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $occupant->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
