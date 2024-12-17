<?php

namespace App\Http\Controllers\Functions;
use App\Constants\Features\TablesName;
use App\Models\Features\Unit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\FeatureResources\UnitResource;
use DB;


class SearchController extends Controller
{
    public function __invoke(Request $request)
    {

        $query = strtolower($request->input('query'));
        $type = strtolower($request->input('type'));


        // dd($type);
        // $units = Unit::whereHas('labels', function ($q) use ($query) {
        //         $q->whereRaw('lower(value) like ?', ['%' . strtolower($query) . '%']);
        //     })
        //     ->get();
        // dd($units->toArray());
        // dd($units);
        // $features = DB::table('features')
        //     ->select('labels.language_tag', 'labels.value', 'features.feature_id', 'features.feature_type')
        //     ->join('feature_label', 'features.feature_id', '=', 'feature_label.feature_id')
        //     ->join('labels', 'labels.id', '=', 'feature_label.label_id')
        //     ->whereRaw('lower(labels.value) like ?', ['%' . strtolower($query) . '%'])
        //     ->where('labels.language_tag', 'LIKE', 'vi')
        //     ->whereIn('features.feature_type', ['unit', 'anchor', 'amenity'])
        //     ->get();
        $tableName = '';
        $tableRelateLabel = '';
        $tableLabel = '';
        switch ($type) {
            case 'unit':
                $tableName = TablesName::UNITS;
                $tableLabel = TablesName::LABELS;
                $tableRelateLabel = TablesName::UNIT_LABELS;
                break;
            case 'amenity':
                $tableName = TablesName::AMENITIES;
                $tableLabel = TablesName::LABELS;
                $tableRelateLabel = TablesName::AMENTITY_LABEL;
                break;
            default:
                return response()->json(['message' => 'Not found'], 404);
        }

        $features = DB::table($tableName . ' as feature')
            ->select('labels.language_tag', 'labels.value', 'feature.' . $type . '_id')
            ->join($tableRelateLabel . ' as feature_label', 'feature.' . $type . '_id', '=', 'feature_label.' . $type . '_id')
            ->join($tableLabel . ' as labels', 'labels.id', '=', 'feature_label.label_id')
            ->whereRaw('lower(labels.value) like ?', ['%' . strtolower($query) . '%'])
            ->where('labels.language_tag', 'LIKE', 'vi')
            // ->whereIn('features.feature_type', ['unit', 'anchor', 'amenity'])
            ->get();

        // dd($features);
        //$unitsResource = UnitResource::collection($units);
        // dd($features);

        return response()->json($features);
    }
}
