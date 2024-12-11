<?php

namespace App\Rules\Venue;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateWebsiteUri implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // $regex = '/^(https?):\/\/[^\s\/$.?#].[^\s]*$/';
        $regex = '/^(https?):\/\/([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/';
        if (!preg_match($regex, $value)) { 
            $fail('The website URI must comply with RFC 3986 and use either the http or https scheme');
        }

    }
}
