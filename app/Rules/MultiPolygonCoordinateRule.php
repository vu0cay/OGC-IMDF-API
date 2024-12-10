<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultiPolygonCoordinateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^\[\[\[\[.*\]\]*\]\]$/';
        // dd(preg_match($pattern, json_encode($value)));
        if(preg_match($pattern, json_encode($value)) === 0) {
            $fail('invalid MultiPolygon coordinate');
        }
    }
}
