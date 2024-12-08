<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\Category\VenueCategory;
use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\VenueResource;
use App\Models\Features\Venue;
use App\Models\FeaturesCategory\Label;
use App\Rules\UniqueLangueTag;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        // $venues = Venue::with('feature', 'restriction', 'category', 'labels')->get();
        $venues = Venue::get();
        $venuesResource = VenueResource::collection($venues);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
         // validation
         $attributes = Validator::make($request->all(), [
            'id' => 'required|uuid|unique:'.TablesName::VENUES.',venue_id',
            'feature_type' => 'required|string|in:venue',
            'geometry' => 'required',
            'lower(geometry.type)' => 'in:polygon',    
            'properties.category' => 'required|string|in:'.VenueCategory::getConstansAsString(),
            'properties.restriction' => 'nullable|string|in:'.RestrictionCategory::getConstansAsString(),
            'properties.name' => 'nullable',
            'properties.alt_name' => 'nullable',
            'properties.hours' => 'required|string',
            'properties.website' => 'required|string',
            'properties.phone' => 'required|string',
            'properties.display_point' => 'required',
            'lower(properties.display_point.type)' => 'in:point',
            'properties.address_id' => 'required|exists:'.TablesName::ADDRESSES.',address_id'
        ]);

        // Bad Request
        if($attributes->fails()) {
            $error = $attributes->errors()->first();
            return response()->json(['success' => false, 'message' => $error], 400);
        }

        // adding feature to the database

        // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
        $textPolygon = Geom::GeomFromText($request->geometry);
        // $textPolygon = Geometry::GeomFromText($request["geometry"]);
        // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
        // $txtPoint = Geometry::GeomFromText($request["properties"]["display_point"]);  
        $txtPoint = Geom::GeomFromText($request->properties["display_point"]);

        // dd($request->properties["name"]);

        $venue = Venue::create([
            'venue_id' => $request->id,
            'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
            'venue_category_id' => DB::table(TablesName::VENUE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
            'geometry' => DB::raw($textPolygon),
            'restriction_category_id' => $request->properties["restriction"] != null 
                        ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                        : null,
            'hours' => $request->properties['hours'],
            'website' => $request->properties['website'] ,
            'phone' => $request->properties['phone'] ,
            'display_point' => DB::raw(value: $txtPoint),
            'address_id' => $request->properties['address_id']       
        ]);
        
        // label name
        FeatureService::AddFeatureLabel($request->properties["name"], 
                                        'name', 
                                        'venue_id', 
                                        TablesName::VENUE_LABELS,
                                        $venue->venue_id);
        // label alt_name
        FeatureService::AddFeatureLabel($request->properties["alt_name"], 
                                        'alt_name', 
                                        'venue_id', 
                                        TablesName::VENUE_LABELS,
                                        $venue->venue_id);

        $venueResource = VenueResource::collection([$venue]);

        // return response 
        return response()->json(['success' => true, 'data' => $venueResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($venue_id) {

        $venue = Venue::query()
                    // ->with('feature', 'restriction', 'category')
                    ->where('venue_id', '=',$venue_id)->first();
        
        if (!$venue) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);


        $venuesResource = VenueResource::collection([$venue]);
        
        $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
        $geojson = json_decode($geojson);
        $geojson->features = $venuesResource;
        
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
    public function update(Request $request, $venue_id)
    {
        // check if the address feature exists
        $venue = Venue::query()
                    ->where('venue_id', '=', $venue_id)->first();
        if (!$venue) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);

        // validate
        $attributes = Validator::make($request->all(), [
            'id' => 'required|uuid|in:'.$venue_id,
            'feature_type' => 'required|string|in:venue',
            'geometry' => 'required',
            'lower(geometry.type)' => 'in:polygon',    
            'properties.category' => 'required|string|in:'.VenueCategory::getConstansAsString(),
            'properties.restriction' => 'nullable|string|in:'.RestrictionCategory::getConstansAsString(),
            'properties.name' => 'nullable',
            'properties.alt_name' => 'nullable',
            'properties.hours' => 'required|string',
            'properties.website' => 'required|string',
            'properties.phone' => 'required|string',
            'properties.display_point' => 'required',
            'lower(properties.display_point.type)' => 'in:point',
            'properties.address_id' => 'required|exists:'.TablesName::ADDRESSES.',address_id'
        ]);
        
        // Bad Request
        if($attributes->fails()) {
            $error = $attributes->errors()->first();
            return response()->json(['success' => false, 'message' => $error], 400);
        }
        
        
        // update to the database
        
        // convert coordinate Polygon to 4236 geometry format: POLYGON( (x1 y1), (x2 y2), ..., (x3 y3) )
        $textPolygon = Geom::GeomFromText($request->geometry);
        // $textPolygon = Geometry::GeomFromText($request["geometry"]);
        // convert coordinates Point to 4236 geometry format: Point( x1 y1 )
        // $txtPoint = Geometry::GeomFromText($request["properties"]["display_point"]);  
        $txtPoint = Geom::GeomFromText($request->properties["display_point"]);
        
        // dd($request->properties["name"]);
        
        $venue->update([
            'venue_id' => $request->id,
            'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
            'venue_category_id' => DB::table(TablesName::VENUE_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
            'geometry' => DB::raw($textPolygon),
            'restriction_category_id' => $request->properties["restriction"] != null 
                        ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                        : null,
            'hours' => $request->properties['hours'],
            'website' => $request->properties['website'] ,
            'phone' => $request->properties['phone'] ,
            'display_point' => DB::raw(value: $txtPoint),
            'address_id' => $request->properties['address_id']       
        ]);
        
        // label name
        FeatureService::UpdateFeatureLabel($request->properties["name"],
                                            'name',
                                            'venue_id',
                                            TablesName::VENUE_LABELS,
                                            $venue->venue_id
        );
        // label alt_name
        FeatureService::UpdateFeatureLabel($request->properties["alt_name"],
                                            'alt_name',
                                            'venue_id',
                                            TablesName::VENUE_LABELS,
                                            $venue->venue_id
        );
        

        // change to IMDF json format 
        $venueResource = VenueResource::collection([$venue]);

        // return response 
        return response()->json(['success' => true, 'data' => $venueResource], 200);

      
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($venue_id)
    {
        $venue = Venue::query()
                    ->where('venue_id', '=',$venue_id)->first();
        
        if (!$venue) return response()->json(['success'=> false, 'message'=> 'Not Found'],404);

        $venue->labels()->delete();
        $venue->delete();

        return response()->json(['success'=> true,'message'=> 'Delete successfully'],204);
    }
}
