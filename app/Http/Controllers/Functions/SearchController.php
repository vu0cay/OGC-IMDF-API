<?php

namespace App\Http\Controllers\Functions;
use App\Models\Features\Unit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FeatureResources\UnitResource;
use DB;


class SearchController extends Controller
{
    public function __invoke(Request $request) {

        $query = strtolower($request->input('query'));

        $units = Unit::with('feature', 'restriction', 'category', 'accessibilities', 'labels')
            ->whereHas('labels', function ($q) use ($query) {
                $q->whereRaw('lower(value) like ?', ['%' . strtolower($query) . '%']);
            })
            ->get();

        $features = DB::table('features')
            ->select('labels.language_tag', 'labels.value', 'features.feature_id', 'features.feature_type')
            ->join('feature_label', 'features.feature_id', '=', 'feature_label.feature_id')
            ->join('labels', 'labels.id', '=', 'feature_label.label_id')
            ->whereRaw('lower(labels.value) like ?', ['%' . strtolower($query) . '%'])
            ->where('labels.language_tag', 'LIKE', 'vi')
            ->whereIn('features.feature_type', ['unit', 'anchor', 'amenity'])
            ->get();

        //$unitsResource = UnitResource::collection($units);
        
        
        return response()->json($features );
    }
}
