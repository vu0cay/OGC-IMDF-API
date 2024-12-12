<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LineStringCoordinateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $regex = '/^\[\[(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)\](,\s*\[\-?\d+(\.\d+)?,\s*\-?\d+(\.\d+)?\])+\]$/';
        if (!preg_match($regex, json_encode($value))) { 
            $fail('invalid coordinates of LineString');
        }
    }
}
