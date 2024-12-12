<?php

namespace App\Http\Controllers\Features;

use App\Constants\Features\TablesName;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AddressResource;
use App\Models\Features\Address;
use App\Rules\Address\AddressUnitMustNotBeBlank;
use App\Rules\Address\ValidateProvince;
use App\Rules\ValidateFeatureIDUnique;
use App\Rules\ValidateIso3166;
use App\Rules\ValidateIso3166_2;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $addresses = Address::get();
            $addressesResource = AddressResource::collection($addresses);
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';

            //$geojson = '{"type": "FeatureCollection","features": []}';
            $geojson = json_decode($geojson);
            $geojson->features = $addressesResource;

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
                // 'id' => 'required|uuid|unique:' . TablesName::ADDRESSES . ',address_id',
                'id' => ['required', 'uuid', new ValidateFeatureIDUnique],
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:address',
                'geometry' => 'nullable|in:null',
                'properties.address' => 'required|string',
                'properties.unit' => ['nullable', new AddressUnitMustNotBeBlank],
                'properties.locality' => 'required|string',
                'properties.province' => ['nullable', 'string', new ValidateIso3166_2],
                'properties.country' => [
                        'required',
                        'string',
                        new ValidateIso3166,
                        function ($attribute, $value, $fail) use ($request) {
                            if (!isset($request->properties['province']))
                                return;
                            $province = explode('-', $request->properties['province'])[0];
                            // dd($province);
                            if ($value !== $province)
                                $fail('The country code not match with province ISO-3166-2');
                        }
                    ],
                'properties.postal_code' => ['nullable'],
                'properties.postal_code_ext' => ['nullable', 'string'],
                'properties.postal_code_vanity' => ['nullable', 'string']
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }

            // adding feature to the database 
            $address = Address::create([
                'address_id' => $request->id,
                'feature_id' => DB::table(TablesName::FEATURES)->where("feature_type", $request->feature_type)->first()->id,
                'address' => $request->properties['address'],
                'unit' => isset($request->properties['unit']) ? $request->properties['unit'] : null,
                'locality' => $request->properties['locality'],
                'province' => $request->properties['province'],
                'country' => $request->properties['country'],
                'postal_code' => isset($request->properties['postal_code']) ? $request->properties['postal_code'] : null,
                'postal_code_ext' => isset($request->properties['postal_code_ext']) ? $request->properties['postal_code_ext'] : null,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        $addressesResource = AddressResource::collection([$address]);

        // return response 
        return response()->json(['success' => true, 'data' => $addressesResource], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($address_id)
    {
        try{
        
            $address = Address::query()
                ->where('address_id', '=', $address_id)->first();
    
            if (!$address)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);
    
            $addressesResource = AddressResource::collection([$address]);
    
            $geojson = '{"type": "FeatureCollection","features": [], "crs": {"type": "name", "properties": {"name": "urn:ogc:def:crs:EPSG::404000"}}}';
            $geojson = json_decode($geojson);
            $geojson->features = $addressesResource;
    
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
     * Full Update
     */
    public function update(Request $request, $address_id)
    {
        try {
            // check if the address feature exists
            $address = Address::query()
                ->where('address_id', '=', $address_id)->first();
            if (!$address)
                return response()->json(['success' => false, 'message' => 'Not Found'], 404);

            // validation
            $attributes = Validator::make($request->all(), [
                'id' => 'required|uuid|in:' . $address_id,
                'type' => 'in:Feature',
                'feature_type' => 'required|string|in:address',
                'geometry' => 'nullable|in:null',
                'properties.address' => 'required|string',
                'properties.unit' => 'nullable|string',
                'properties.locality' => 'required|string',
                'properties.province' => ['nullable', 'string', new ValidateIso3166_2],
                'properties.country' => [
                        'required',
                        'string',
                        new ValidateIso3166,
                        'in:' . json_encode(explode('-', $request->properties['province'])[0])
                    ],
                'properties.postal_code' => ['nullable'],
                'properties.postal_code_ext' => ['nullable', 'string'],
                'properties.postal_code_vanity' => ['nullable', 'string']
            ]);

            // Bad Request
            if ($attributes->fails()) {
                $error = $attributes->errors()->first();
                return response()->json(['success' => false, 'message' => $error], 400);
            }


            // update to the database
            $rows = $address->update([
                'address_id' => $request->id,
                'address' => $request->properties['address'],
                'unit' => $request->properties['unit'],
                'locality' => $request->properties['locality'],
                'province' => $request->properties['province'],
                'country' => $request->properties['country'],
                'postal_code' => $request->properties['postal_code'],
                'postal_code_ext' => $request->properties['postal_code_ext'],
            ]);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], status: 400);
        }

        // change to IMDF json format 
        $addressesResource = AddressResource::collection([$address]);
        // return response 
        return response()->json(['success' => true, 'data' => $addressesResource], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($address_id)
    {

        $address = Address::query()
            ->where('address_id', '=', $address_id)->first();

        if (!$address)
            return response()->json(['success' => false, 'message' => 'Not Found'], 404);

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Delete successfully'], 204);
    }
}
