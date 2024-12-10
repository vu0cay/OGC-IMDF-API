<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\AmenityCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AmenityResource;
use App\Models\Features\Amenity;
use App\Rules\PointCoordinateRule;
use App\Rules\ValidateFeatureIDUnique;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $amenities = Amenity::with('feature', 'units', 'category', 'labels', 'accessibilities')->get();
        $amenities = Amenity::get();
        $amenitiesResource = AmenityResource::collection($amenities);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $amenitiesResource;

        return response()->json([$geojson], 200);
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
                'feature_type' => 'required|string|in:amenity',
                'geometry' => 'required',
                'geometry.type' => ['required','in:Point'],
                'geometry.coordinates' => ['required', new PointCoordinateRule],
                'properties.category' => 'required|string|in:' . AmenityCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array'],
                'properties.name.*' => 'required',
                'properties.short_name' => ['nullable', 'array'],
                'properties.short_name.*' => 'required',
                'properties.phone' => 'nullable',
                'properties.website' => 'nullable',
                'properties.hours' => 'nullable',
                'properties.correlation_id' => ['nullable','uuid'],
                'properties.address_id' => 'nullable|uuid|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.unit_ids' => 'required|array',
                'properties.unit_ids.*' => 'required|uuid|exists:' . TablesName::UNITS . ',unit_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            // $textPolygon = Geom::GeomFromText($request->geometry);

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->geometry);

            // Start the transaction
            DB::beginTransaction();
            $amenity = Amenity::create([
                'amenity_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'amenity_category_id' => DB::table(TablesName::AMENITY_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($txtPoint),
                'phone' => $request->properties['phone'],
                'website' => $request->properties['website'],
                'hours' => $request->properties['hours'],
                'correlation_id' => $request->properties['correlation_id'],
            ]);

            // add unit accessibility
            collect($request->properties['accessibility'])->map(function ($item) use ($amenity) {
                $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                DB::table(TablesName::AMENITY_ACCESSIBILITY)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'accessibility_id' => $accessibility_id
                ]);
            });

            // add amenity address
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_AMENITIES)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // add buildings
            collect($request->properties['unit_ids'])->map(function ($item) use ($amenity) {
                DB::table(TablesName::AMENITY_UNIT)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'unit_id' => $item
                ]);
            });

            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'amenity_id',
                TablesName::AMENTITY_LABEL,
                $amenity->amenity_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"],
                'alt_name',
                'amenity_id',
                TablesName::AMENTITY_LABEL,
                $amenity->amenity_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $amenityResource = AmenityResource::collection([$amenity]);
        return response()->json(['success' => true, 'data' => $amenityResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($amenity_id)
    {
        $amenity = Amenity::query()
                    ->where('amenity_id', '=', $amenity_id)->first();
        
        if(!$amenity) return response()->json( ['success' => false, 'message'=> 'Not Found'],404);


        $amenitysResource = AmenityResource::collection([$amenity]);

        // $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

        $geojson = json_decode($geojson);
        $geojson->features = $amenitysResource;

        return response()->json([$geojson], 200);
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
    public function update(Request $request, $amenity_id)
    {
        try {
            // check if the address feature exists
            $amenity = Amenity::query()
                ->where('amenity_id', '=', $amenity_id)->first();
            if (!$amenity)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$amenity_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:amenity',
                'geometry' => 'required',
                'geometry.type' => ['required','in:Point'],
                'geometry.coordinates' => ['required', new PointCoordinateRule],
                'properties.category' => 'required|string|in:' . AmenityCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array'],
                'properties.name.*' => 'required',
                'properties.short_name' => ['nullable', 'array'],
                'properties.short_name.*' => 'required',
                'properties.phone' => 'nullable',
                'properties.website' => 'nullable',
                'properties.hours' => 'nullable',
                'properties.correlation_id' => ['nullable','uuid'],
                'properties.address_id' => 'nullable|uuid|exists:' . TablesName::ADDRESSES . ',address_id',
                'properties.unit_ids' => 'required|array',
                'properties.unit_ids.*' => 'required|uuid|exists:' . TablesName::UNITS . ',unit_id',
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // Adding feature to the database
            // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
            // $textPolygon = Geom::GeomFromText($request->geometry);

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->geometry);

            // Start the transaction
            DB::beginTransaction();
            $amenity->update([
                'amenity_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'amenity_category_id' => DB::table(TablesName::AMENITY_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($txtPoint),
                'phone' => $request->properties['phone'],
                'website' => $request->properties['website'],
                'hours' => $request->properties['hours'],
                'correlation_id' => $request->properties['correlation_id'],
            ]);

            // add unit accessibility

            $record = DB::table(TablesName::AMENITY_ACCESSIBILITY)
                ->where('amenity_id', $amenity->amenity_id)
                ->delete();

            collect($request->properties['accessibility'])->map(function ($item) use ($amenity) {
                $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                DB::table(TablesName::AMENITY_ACCESSIBILITY)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'accessibility_id' => $accessibility_id
                ]);
            });

            // add amenity address
            $record = DB::table(TablesName::ADDRESS_AMENITIES . ' as address_feature')
                ->where('amenity_id', $amenity->amenity_id)
                ->delete();

            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_AMENITIES)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // add units

            $record = DB::table(TablesName::AMENITY_UNIT)
                ->where('amenity_id', $amenity->amenity_id)
                ->delete();

            collect($request->properties['unit_ids'])->map(function ($item) use ($amenity) {
                DB::table(TablesName::AMENITY_UNIT)->insert([
                    'amenity_id' => $amenity->amenity_id,
                    'unit_id' => $item
                ]);
            });

            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"],
                'name',
                'amenity_id',
                TablesName::AMENTITY_LABEL,
                $amenity->amenity_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"],
                'alt_name',
                'amenity_id',
                TablesName::AMENTITY_LABEL,
                $amenity->amenity_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $amenityResource = AmenityResource::collection([$amenity]);
        return response()->json(['success' => true, 'data' => $amenityResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($amenity_id)
    {
        try{

            $amenity = Amenity::query()
            ->where('amenity_id', '=', $amenity_id)->first();
    
            if (!$amenity)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
            
            $amenity->delete();
    
            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
