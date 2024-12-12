<?php

namespace App\Rules\Occupant;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateValidity implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $arr = ['start', 'end', 'modified'];
        foreach ($value as $key => $val) { 
            if(!in_array($key, $arr)) { 
                $fail('invalid validity occupant.');
            }
        }
    }
}
