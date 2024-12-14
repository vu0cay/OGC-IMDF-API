<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultiLineStringCoordinateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^\[\s*(\[\s*(\[\s*(-?\d+(\.\d+)?\s*,\s*)*-?\d+(\.\d+)?\s*\]\s*,?\s*)+\]\s*,?\s*)+\]$/';
        if (preg_match($regex, json_encode($value)) === 0) { 
            $fail('invalid MultiLineString coordinate');
        }
        
    }
}
