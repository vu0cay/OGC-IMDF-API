<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\BuildingCategory;
use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\BuildingResource;
use App\Models\Features\Building;
use App\Rules\PointCoordinateRule;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $buildings = Building::with('feature', 'restriction', 'category')->get();
        $buildings = Building::get();
        $buildingsResource = BuildingResource::collection($buildings);

        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;

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
                'id' => 'required|uuid|unique:' . TablesName::BUILDINGS . ',building_id',
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:building',
                'geometry' => 'nullable|in:null',
                'properties.category' => 'required|string|in:' . BuildingCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.name' => 'nullable|array',
                'properties.name.*' => 'required',
                'properties.alt_name' => 'nullable|array',
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'required',
                'properties.display_point.type' => 'required|in:Point',
                'properties.display_point.coordinates' => ['required', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id'
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // add feature here ....
            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);


            // Start the transaction
            DB::beginTransaction();


            // add buildings
            $building = Building::create([
                'building_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'building_category_id' => DB::table(TablesName::BUILDING_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);

            // add building address
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_BUILDINGS)->insert([
                    'building_id' => $building->building_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"],
                'name',
                'building_id',
                TablesName::BUILDING_LABELS,
                $building->building_id
            );
            // label alt_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"],
                'alt_name',
                'building_id',
                TablesName::BUILDING_LABELS,
                $building->building_id
            );

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        // change to IMDF json format 
        $buildingResource = BuildingResource::collection([$building]);

        // return response 
        return response()->json(['success' => true, 'data' => $buildingResource], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show($building_id)
    {
        $buildings = Building::query()
            ->where('building_id', '=', $building_id)
            // ->with('feature', 'restriction', 'category')
            ->first();

        if (!$buildings)
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);

        $buildingsResource = BuildingResource::collection([$buildings]);

        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $buildingsResource;

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
    public function update(Request $request, $building_id)
    {
        try {
            // check if the address feature exists
            $building = Building::query()
                ->where('building_id', '=', $building_id)->first();
            if (!$building)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => 'required|uuid|in:' . $building_id,
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:building',
                'geometry' => 'nullable|in:null',
                'properties.category' => 'required|string|in:' . BuildingCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.name' => 'nullable|array',
                'properties.name.*' => 'required',
                'properties.alt_name' => 'nullable|array',
                'properties.alt_name.*' => 'required',
                'properties.display_point' => 'required',
                'properties.display_point.type' => 'required|in:Point',
                'properties.display_point.coordinates' => ['required', new PointCoordinateRule],
                'properties.address_id' => 'nullable|exists:' . TablesName::ADDRESSES . ',address_id'
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // add feature here ....

            // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
            $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

            // Start the transaction
            DB::beginTransaction();

            // add buildings
            $building->update([
                'building_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'building_category_id' => DB::table(TablesName::BUILDING_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'display_point' => DB::raw(value: $txtPoint)
            ]);

            // add building address
            $record = DB::table(TablesName::ADDRESS_BUILDINGS . ' as address_feature')
                ->where('building_id', $building->building_id)
                ->delete();

            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_BUILDINGS)->insert([
                    'building_id' => $building->building_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"],
                'name',
                'building_id',
                TablesName::BUILDING_LABELS,
                $building->building_id
            );
            // label alt_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"],
                'alt_name',
                'building_id',
                TablesName::BUILDING_LABELS,
                $building->building_id
            );

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        // change to IMDF json format 
        $buildingResource = BuildingResource::collection([$building]);

        // return response 
        return response()->json(['success' => true, 'data' => $buildingResource], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($building_id)
    {
        try{

            $building = Building::query()
            ->where('building_id', '=', $building_id)->first();
    
            if (!$building)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
    
            // $building->address()->delete();
            // $building->labels()->delete();
            $building->delete();
    
            return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }
    }
}
