<?php

namespace App\Contracts;

use App\Constants\Features\TablesName;
use App\Models\Features\Feature;
use App\Models\FeaturesCategory\FeatureReference;
use DB;


class FeatureReferenceRelation
{
    public static function getFeatureRelation($this_origin)
    {

        $feature_reference_id = $this_origin->feature_reference_id;
        $origin = isset($feature_reference_id) ?
            FeatureReference::find($feature_reference_id)
            : null;

        if ($origin) {
            $feature_type = Feature::find($origin->feature_type_id);
            $origin = ["id" => $origin->feature_id, "feature_type" => $feature_type->feature_type];
        }

        return $origin;
    }
    public static function getFeatureIntermerdiary($this_origin)
    {
        $collect = [];
        foreach ($this_origin as $value) {
            $value = $value->toArray();
            $feature_reference_id = $value['pivot']['feature_reference_id'];
            // dd($feature_reference_id);

            $inter = FeatureReference::find($feature_reference_id);
            $inter = $inter->toArray();
            $feature_type = Feature::find($inter["feature_type_id"]);

            array_push($collect, ["id" => $inter["feature_id"], "feature_type" => $feature_type->feature_type]);

        }
        // dd($collect);
        return $collect;
    }
    public static function AddFeatureReferenceRelation($feature_refer, $tablename, $relationship_id)
    {
        // dd($feature_refer);

        if ($feature_refer) {
            $featureRef = FeatureReference::where('feature_id', $feature_refer['id'])->first();
            // dd($featureRef);
            $finalVal = !isset($featureRef) ?
                DB::table(TablesName::FEATURE_REFERENCES)->insert([
                    'feature_id' => $feature_refer['id'],
                    'feature_type_id' => Feature::where('feature_type', $feature_refer['feature_type'])->first()->id
                ]) : $featureRef;
            DB::table($tablename)->insert([
                'feature_reference_id' => $finalVal->id,
                'relationship_id' => $relationship_id
            ]);

        }
    }
    public static function UpdateFeatureReferenceRelation($feature_refer, $tablename, $relationship_id)
    {

        $record = DB::table($tablename . ' as feature_relation')->delete();

        if ($feature_refer) {
            $featureRef = FeatureReference::where('feature_id', $feature_refer['id'])->first();
            $finalVal = !isset($featureRef) ?
                DB::table(TablesName::FEATURE_REFERENCES)->insert([
                    'feature_id' => $feature_refer['id'],
                    'feature_type_id' => Feature::where('feature_type', $feature_refer['feature_type'])->first()->id
                ]) : $featureRef;
            DB::table($tablename)->insert([
                'feature_reference_id' => $finalVal->id,
                'relationship_id' => $relationship_id
            ]);

        }
    }

    public static function AddFeatureIntermediary($feature_refer, $tablename, $relationship_id)
    {

        if ($feature_refer) {
            foreach ($feature_refer as $value) {
                $featureRef = FeatureReference::where('feature_id', $value['id'])->first();
                // dd($featureRef);
                $finalVal = !isset($featureRef) ?
                    DB::table(TablesName::FEATURE_REFERENCES)->insert([
                        'feature_id' => $value['id'],
                        'feature_type_id' => Feature::where('feature_type', $value['feature_type'])->first()->id
                    ]) : $featureRef;
                DB::table($tablename)->insert([
                    'feature_reference_id' => $finalVal->id,
                    'relationship_id' => $relationship_id
                ]);
            }
        }
    }

    public static function UpdateFeatureIntermediary($feature_refer, $tablename, $relationship_id)
    {
        $record = DB::table($tablename . ' as feature_relation')->delete();

        if ($feature_refer) {
            foreach ($feature_refer as $value) {
                $featureRef = FeatureReference::where('feature_id', $value['id'])->first();
                // dd($featureRef);
                $finalVal = !isset($featureRef) ?
                    DB::table(TablesName::FEATURE_REFERENCES)->insert([
                        'feature_id' => $value['id'],
                        'feature_type_id' => Feature::where('feature_type', $value['feature_type'])->first()->id
                    ]) : $featureRef;
                DB::table($tablename)->insert([
                    'feature_reference_id' => $finalVal->id,
                    'relationship_id' => $relationship_id
                ]);
            }
        }
    }

}