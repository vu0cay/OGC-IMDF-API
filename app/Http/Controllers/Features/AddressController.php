<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureResources\AddressResource;
use App\Models\Features\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        
        $addresses = Address::with('feature', 'restriction', 'category', 'labels')->get();
        $addressesResource = AddressResource::collection($addresses);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $addressesResource;
        
        return $geojson;
        
    }
    public function show($address_id) {

        $address = Address::query()
                    ->with('feature', 'restriction', 'category')
                    ->where('address_id', '=',$address_id)->first();
        $addresssResource = addressResource::collection([$address]);
        
        $geojson = '{"type": "FeatureCollection","features": []}';
        $geojson = json_decode($geojson);
        $geojson->features = $addresssResource;
        
        return $geojson;
    }
}
