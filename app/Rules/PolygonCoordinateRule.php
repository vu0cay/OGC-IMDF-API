<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PolygonCoordinateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // valid matches 
        // [[[1,2],[3.5,-4],[5,-6.7],[7.8,9]]]
        $pattern = '/^\[\[((\[-?\d+(\.\d+)?,-?\d+(\.\d+)?\],){3,}\[-?\d+(\.\d+)?,-?\d+(\.\d+)?\])\]\]$/';
        if(preg_match($pattern, json_encode($value)) === 0) {
            $fail('invalid Polygon coordinate');
        }
    }
}
