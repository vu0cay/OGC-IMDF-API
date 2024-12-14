<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateHours implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^(Mo|Tu|We|Th|Fr|Sa|Su)-(Mo|Tu|We|Th|Fr|Sa|Su) \d{2}:\d{2}-\d{2}:\d{2}$/';

        if (!preg_match($regex, $value)) { 
            $fail('Hours must follow the OSM Opening Hours format, for example: Mo-Fr 08:30-20:00.');
        }

    }
}
