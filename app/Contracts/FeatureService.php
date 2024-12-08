<?php

namespace App\Contracts;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use DB;


class FeatureService 
{
    public static function AddFeatureLabel($labels, $type, $feature_name_id, $tableName, $feature_id) {
        $record = DB::table(TablesName::LABELS.' as labels')
                    ->join($tableName.' as feature_label','labels.id','=','feature_label.label_id')
                    ->where($feature_name_id, $feature_id)
                    ->where('feature_label.type',$type)
                    ->delete();
            
        if($labels == null) return;

        foreach($labels as $key => $value) { 
            $record = DB::table(TablesName::LABELS.' as labels')
                            ->join($tableName.' as feature_label','labels.id','=','feature_label.label_id')
                            ->where('language_tag', $key)
                            ->where($feature_name_id, $feature_id)
                            ->where('feature_label.type',$type)
                            ->first();
            if($record) continue;
            $newLabel = Label::create([
                'language_tag' => $key,
                'value' => $value
            ]);
            DB::table($tableName)->insert([
                $feature_name_id => $feature_id,
                'label_id' => $newLabel->id,
                'type' => $type
            ]); 
        }
       
    }
    public static function UpdateFeatureLabel($labels, $type, $feature_name_id, $tableName, $feature_id) {
        
        $record = DB::table(TablesName::LABELS.' as labels')
                    ->join($tableName.' as feature_label','labels.id','=','feature_label.label_id')
                    ->where($feature_name_id, $feature_id)
                    ->where('feature_label.type',$type)
                    ->delete();
        

        if($labels == null) return;
        foreach($labels as $key => $value) { 
            $record = DB::table(TablesName::LABELS.' as labels')
                            ->join($tableName.' as feature_label','labels.id','=','feature_label.label_id')
                            ->where('language_tag', $key)
                            ->where($feature_name_id, $feature_id)
                            ->where('feature_label.type',$type)
                            ->first();   
            if($record) {
                $existingLabel = Label::find($record->label_id);
                $existingLabel->value = $value;
                $existingLabel->save();
            }                                        
            else {
                $newLabel = Label::create([
                    'language_tag' => $key,
                    'value' => $value
                ]);
                DB::table($tableName)->insert([
                    $feature_name_id => $feature_id,
                    'label_id' => $newLabel->id,
                    'type' => $type
                ]);
            }
        }
       
    }
}