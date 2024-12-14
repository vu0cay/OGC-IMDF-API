<?php

namespace App\Rules\Relationship;

use App\Models\Features\Feature;
use Closure;
use DB;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateFeatureReference implements ValidationRule
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

        if (!isset($value["id"]) || !isset($value["feature_type"])) {
            $fail("field missing id or feature_type");
            return;
        }

        $row = Feature::where("feature_type", $value["feature_type"])->first();
        if (!$row) {
            $fail("Not found feature type of " . $value["feature_type"]);
            return;
        }

        if (substr($value["feature_type"], -1) === 's') {
            $row = DB::table($value["feature_type"] . 'es')->where($value["feature_type"] . '_id', $value["id"])->first();
        } else
            $row = DB::table($value["feature_type"] . 's')->where($value["feature_type"] . '_id', $value["id"])->first();

        if (!$row) {
            $fail('Not found ' . $value["id"] . ' in ' . $value["feature_type"]);
        }
    }
}
