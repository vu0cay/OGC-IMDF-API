<?php

namespace App\Rules\Venue;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidatePhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^\+[1-9]\d{1,14}$/';
        if (!preg_match($regex, $value)) { 
            $fail('The phone number must comply with E.164 (RFC 6116)');
        }
    }
}
