<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\RelationshipCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureReferenceRelation;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\RelationshipResource;
use App\Models\Features\Relationship;
use App\Models\FeaturesCategory\FeatureReference;
use App\Rules\Relationship\ValidateFeatureReference;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            $relationships = Relationship::get();
            $relationshipsResource = RelationshipResource::collection($relationships);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $relationshipsResource;

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
                'feature_type' => 'required|string|in:relationship',
                'properties.category' => 'required|string|in:' . RelationshipCategory::getConstansAsString(),
                'properties.hours' => 'nullable|string',
                'properties.origin' => ['nullable', new ValidateFeatureReference],
                'properties.intermediary' => ['nullable', new ValidateFeatureReference],
                'properties.destination' => ['nullable', new ValidateFeatureReference],
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database

            // Start the transaction
            DB::beginTransaction();
            // DB::table(TablesName::RELATIONSHIPS)->insert([
            //     'relationship_id' => "88888888-8888-1234-8888-888888888888",
            //     'relationship_category_id' => 6,
            //     'direction' => 'directed',
            //     'feature_id' => 13,
            //     // 'origin_id' => 1,
            //     // 'intermediary_id' => 2,
            // ]);

            $relationship = Relationship::create([
                'relationship_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'relationship_category_id' => DB::table(TablesName::RELATIONSHIP_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'direction' => $request->properties['direction'],
            ]);

            // add feature reference

            // if(isset($request->properties['origin'])) {
            //     $featureRef = FeatureReference::where('feature_id', $request->properties['origin']['id'])->first();
            //     $finalVal = !isset($featureRef) ?
            //         DB::table(TablesName::FEATURE_REFERENCES)->insert([
            //             'feature_id' => "88888888-8888-8888-8888-888888888888",
            //             'feature_type_id' => 15
            //         ]) : $featureRef;

            //     DB::table(TablesName::FEATURE_ORIGIN_RELATIONSHIPS)->insert([
            //         'feature_reference_id' => $finalVal->id,
            //         'relationship_id' => $relationship->relationship_id
            //     ]);

            // }
            FeatureReferenceRelation::AddFeatureReferenceRelation(
                $request->properties['origin'],
                TablesName::FEATURE_ORIGIN_RELATIONSHIPS,
                $relationship->relationship_id
            );
            FeatureReferenceRelation::AddFeatureReferenceRelation(
                $request->properties['intermediary'],
                TablesName::FEATURE_INTERMEDIARY_RELATIONSHIPS,
                $relationship->relationship_id
            );
            FeatureReferenceRelation::AddFeatureReferenceRelation(
                $request->properties['destination'],
                TablesName::FEATURE_DESTINATION_RELATIONSHIPS,
                $relationship->relationship_id
            );
            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $relationshipResource = RelationshipResource::collection([$relationship]);
        return response()->json(['success' => true, 'data' => $relationshipResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($relationship_id)
    {
        try {
            $relationship = Relationship::query()
                ->where('relationship_id', '=', $relationship_id)->first();

            if (!$relationship)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $relationshipResource = RelationshipResource::collection([$relationship]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $relationshipResource;

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
    public function update(Request $request, $relationship_id)
    {
        try {
            // check if the address feature exists
            $relationship = Relationship::query()
                ->where('relationship_id', '=', $relationship_id)->first();
            if (!$relationship)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:' . $relationship_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:relationship',
                'properties.category' => 'required|string|in:' . RelationshipCategory::getConstansAsString(),
                'properties.hours' => 'nullable|string',
                'properties.origin' => ['nullable', new ValidateFeatureReference],
                'properties.intermediary' => ['nullable', new ValidateFeatureReference],
                'properties.destination' => ['nullable', new ValidateFeatureReference],
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database

            // Start the transaction
            DB::beginTransaction();

            $relationship->update([
                'relationship_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'relationship_category_id' => DB::table(TablesName::RELATIONSHIP_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'direction' => $request->properties['direction'],
            ]);

            // update feature reference

            FeatureReferenceRelation::UpdateFeatureReferenceRelation(
                $request->properties['origin'] ?? null,
                TablesName::FEATURE_ORIGIN_RELATIONSHIPS,
                $relationship->relationship_id
            );
            FeatureReferenceRelation::UpdateFeatureReferenceRelation(
                $request->properties['intermediary'] ?? null,
                TablesName::FEATURE_INTERMEDIARY_RELATIONSHIPS,
                $relationship->relationship_id
            );
            FeatureReferenceRelation::UpdateFeatureReferenceRelation(
                $request->properties['destination'] ?? null,
                TablesName::FEATURE_DESTINATION_RELATIONSHIPS,
                $relationship->relationship_id
            );
            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $relationshipResource = RelationshipResource::collection([$relationship]);
        return response()->json(['success' => true, 'data' => $relationshipResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($relationship_id)
    {
        try {
            $relationship = Relationship::query()
                ->where('relationship_id', '=', $relationship_id)->first();

            if (!$relationship)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            $relationship->delete();

            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
