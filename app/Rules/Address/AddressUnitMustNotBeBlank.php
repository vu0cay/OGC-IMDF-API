<?php

namespace App\Rules\Address;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressUnitMustNotBeBlank implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $regex = '/^[a-zA-Z0-9]+$/';
        if(!preg_match($regex, $value)) {
            $fail("Address Unit must not blank. (Not empty string or white space)");
        }
    }
}
