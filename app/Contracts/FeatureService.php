<?php

namespace App\Contracts;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use DB;
use Exception;


class FeatureService
{
    public static function AddFeatureLabel($labels, $type, $feature_name_id, $tableName, $feature_id)
    {
        try {

            if ($labels == null)
                return;
           
            /**
             * This function retrieves each label in the name properties (e.g., "en": "Ground Floor").
             * 
             * First, it checks the database to see if there is an existing label with the same `language_tag` and `value`.
             * - If yes, we retrieve the `label_id` and add it to the pivot table `Feature_Label`.
             * - If not, we create a new label in the `Label` table and then add the new `label_id` to the pivot table.
             * 
             * I use this method to avoid violating BCNF (Boyce-Codd Normal Form) when adding feature names to the `Label` table.
             * This prevents duplicated rows, for example:
             * - Creating a name "en": "Ground Floor" for venue1.
             * - And another "en": "Ground Floor" for venue2.
             * 
             * Without this approach, the `Label` table would look like this:
             * |1|en|Ground Floor|
             * |2|en|Ground Floor|
             */
            foreach ($labels as $key => $value) {
                $record = DB::table(TablesName::LABELS . ' as labels')
                    ->where('language_tag', $key)
                    ->where('value', $value)
                    ->first();
                if ($record) {
                    DB::table($tableName)->insert([
                        $feature_name_id => $feature_id,
                        'label_id' => $record->id,
                        'type' => $type
                    ]);
                } else {
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
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }
    public static function UpdateFeatureLabel($labels, $type, $feature_name_id, $tableName, $feature_id)
    {


        $record = DB::table($tableName . ' as feature_label')
            ->where($feature_name_id, $feature_id)
            ->where('feature_label.type', $type)
            ->delete();

        if ($labels == null)
            return;

        foreach ($labels as $key => $value) {
            $record = DB::table(TablesName::LABELS . ' as labels')
                ->where('language_tag', $key)
                ->where('value', $value)
                ->first();
            if ($record) {
                DB::table($tableName)->insert([
                    $feature_name_id => $feature_id,
                    'label_id' => $record->id,
                    'type' => $type
                ]);
            } else {
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