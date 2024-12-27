<?php

namespace App\Http\Controllers;

use App\Constants\Features\Category\RestrictionCategory;
use App\Constants\Features\Category\SectionCategory;
use App\Constants\Features\TablesName;
use App\Contracts\FeatureService;
use App\Contracts\Geom;
use App\Http\Resources\FeatureResources\SectionResource;
use App\Models\Features\Section;
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

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $units = Unit::with( 'feature', 'restriction', 'category', 'accessibilities', 'labels')->get();
            $sections = Section::get();
            $sectionsResource = SectionResource::collection($sections);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $sectionsResource;
            // return $feature;
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
                'feature_type' => 'required|string|in:section',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!isset($request->geometry['type']))
                            return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.category' => 'required|string|in:' . SectionCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.address_id' => 'nullable|uuid|exists:' . TablesName::ADDRESSES . ',address_id',
                // 'properties.correlation_id' => ['nullable', 'uuid', 'exists:' . TablesName::SECTIONS . ',section_id'],
                'properties.correlation_id' => ['nullable', 'uuid'],
                'properties.parents' => ['nullable', 'array'],
                'properties.parents.*' => [
                   'uuid', 'required_if:properties.parents,!=null','exists:' . TablesName::SECTIONS . ',section_id'
                ],
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

            $section = Section::create([
                'section_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'section_category_id' => DB::table(TablesName::SECTION_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint)
            ]);



            // add level address
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_SECTIONS)->insert([
                    'section_id' => $section->section_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // add section accessibility
            if (isset($request->properties['accessibility'])) {
                collect($request->properties['accessibility'])->map(function ($item) use ($section) {
                    $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                    DB::table(TablesName::SECTION_ACCESSIBILITY)->insert([
                        'section_id' => $section->section_id,
                        'accessibility_id' => $accessibility_id
                    ]);
                });
            }
            // add section parents 
            // if(isset($request->properties['parents'])) {
            //     foreach($request->properties['parents'] as $parent) { 
            //         DB::table(TablesName::SECTION_PARENTS)->insert([
            //             'section_id' => $section->section_id,
            //             'parent_section_id' => $parent
            //         ]);
            //     }
            // }
            FeatureService::AddFeatureParents(
                $request->properties['parents'] ?? null,
                TablesName::SECTION_PARENTS,
                $section->section_id,
                'section_id',
                'parent_section_id'

            );
            // label name
            FeatureService::AddFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'section_id',
                TablesName::SECTION_LABELS,
                $section->section_id
            );
            // label short_name
            FeatureService::AddFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'section_id',
                TablesName::SECTION_LABELS,
                $section->section_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $sectionResource = SectionResource::collection([$section]);
        return response()->json(['success' => true, 'data' => $sectionResource], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($section_id)
    {
        try {
            $section = Section::query()
                ->where('section_id', '=', $section_id)->first();

            if (!$section)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);


            $sectionsResource = sectionResource::collection([$section]);

            // $geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            $geojson = json_decode($geojson);
            $geojson->features = $sectionsResource;

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
    public function update(Request $request, $section_id)
    {
        try {
            // check if the address feature exists
            $section = Section::query()
                ->where('section_id', '=', $section_id)->first();
            if (!$section)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => ['required', 'uuid', 'in:'.$section_id],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:section',
                'geometry' => 'required',
                'geometry.type' => 'required|in:Polygon,MultiPolygon',
                'geometry.coordinates' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        if (!isset($request->geometry['type']))
                            return;

                        if ($request->geometry['type'] === 'Polygon') {
                            $validateInstance = new PolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        } else {
                            $validateInstance = new MultiPolygonCoordinateRule();
                            $validateInstance->validate($attribute, $value, $fail);
                        }

                    }
                ],
                'properties.category' => 'required|string|in:' . SectionCategory::getConstansAsString(),
                'properties.restriction' => 'nullable|string|in:' . RestrictionCategory::getConstansAsString(),
                'properties.accessibility' => 'nullable|array',
                'properties.accessibility.*' => 'required_if:properties.accessibility,!=null|exists:' . TablesName::ACCESSIBILITY_CATEGORIES . ',name',
                'properties.name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.name.*' => 'required',
                'properties.alt_name' => ['nullable', 'array', new ValidateIso639],
                // 'properties.alt_name.*' => 'required',
                'properties.display_point' => ['nullable', new ValidateDisplayPoint],
                // 'properties.display_point.type' => ['required_if:properties.display_point,!=null', 'in:Point'],
                // 'properties.display_point.coordinates' => ['required_if:properties.display_point,!=null', new PointCoordinateRule],
                'properties.level_id' => 'required|uuid|exists:' . TablesName::LEVELS . ',level_id',
                'properties.address_id' => 'nullable|uuid|exists:' . TablesName::ADDRESSES . ',address_id',
                // 'properties.correlation_id' => ['nullable', 'uuid', 'exists:' . TablesName::SECTIONS . ',section_id'],
                'properties.correlation_id' => ['nullable', 'uuid'],
                'properties.parents' => ['nullable', 'array'],
                'properties.parents.*' => [
                   'uuid', 'required_if:properties.parents,!=null','exists:' . TablesName::SECTIONS . ',section_id'
                ],

                
                
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

            $section->update([
                'section_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'section_category_id' => DB::table(TablesName::SECTION_CATEGORIES)->where("name", $request->properties['category'])->first()->id,
                'geometry' => DB::raw($textPolygon),
                'restriction_category_id' => isset($request->properties["restriction"])
                    ? DB::table(TablesName::RESTRICTION_CATEGORIES)->where("name", $request->properties['restriction'])->first()->id
                    : null,
                'level_id' => $request->properties["level_id"],
                'display_point' => DB::raw(value: $txtPoint)
            ]);



            // add level address
            $record = DB::table(TablesName::ADDRESS_SECTIONS . ' as address_feature')
                ->where('section_id', $section->section_id)
                ->delete();
            if (isset($request->properties['address_id'])) {
                DB::table(TablesName::ADDRESS_SECTIONS)->insert([
                    'section_id' => $section->section_id,
                    'address_id' => $request->properties['address_id']
                ]);
            }

            // add section accessibility
            $record = DB::table(TablesName::SECTION_ACCESSIBILITY)
                ->where('section_id', $section->section_id)
                ->delete();
            if (isset($request->properties['accessibility'])) {
                collect($request->properties['accessibility'])->map(function ($item) use ($section) {
                    $accessibility_id = DB::table(TablesName::ACCESSIBILITY_CATEGORIES)->where('name', $item)->first()->id;
                    DB::table(TablesName::SECTION_ACCESSIBILITY)->insert([
                        'section_id' => $section->section_id,
                        'accessibility_id' => $accessibility_id
                    ]);
                });
            }
            
            FeatureService::UpdateFeatureParents(
                $request->properties['parents'] ?? null,
                TablesName::SECTION_PARENTS,
                $section->section_id,
                'section_id',
                'parent_section_id'

            );
            // label name
            FeatureService::UpdateFeatureLabel(
                $request->properties["name"] ?? null,
                'name',
                'section_id',
                TablesName::SECTION_LABELS,
                $section->section_id
            );
            // label short_name
            FeatureService::UpdateFeatureLabel(
                $request->properties["alt_name"] ?? null,
                'alt_name',
                'section_id',
                TablesName::SECTION_LABELS,
                $section->section_id
            );

            // Commit the transaction
            DB::commit();

        } catch (Exception $e) {

            // Roll back the transaction if there's an error occur.
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $sectionResource = SectionResource::collection([$section]);
        return response()->json(['success' => true, 'data' => $sectionResource], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
