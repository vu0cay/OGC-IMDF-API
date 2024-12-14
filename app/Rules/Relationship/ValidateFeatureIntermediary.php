<?php

namespace App\Rules\Relationship;

use App\Models\Features\Feature;
use Closure;
use DB;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateFeatureIntermediary implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!isset($value))
            return;
        // dd($value);
        foreach ($value as $item) {

            if (!isset($item["id"]) || !isset($item["feature_type"])) {
                $fail("field missing id or feature_type");
                return;
            }

            $row = Feature::where("feature_type", $item["feature_type"])->first();
            if (!$row) {
                $fail("Not found feature type of " . $item["feature_type"]);
                return;
            }

            if (substr($item["feature_type"], -1) === 's') {
                $row = DB::table($item["feature_type"] . 'es')->where($item["feature_type"] . '_id', $item["id"])->first();
            } else
                $row = DB::table($item["feature_type"] . 's')->where($item["feature_type"] . '_id', $item["id"])->first();

            if (!$row) {
                $fail('Not found ' . $item["id"] . ' in ' . $item["feature_type"]);
            }
        }
    }
}
