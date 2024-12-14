<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateDisplayPoint implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if(!is_array($value)) {
            $fail("Display Point must be Point geometry!");
            return;
        }
        $type = $value["type"] ?? null;
        $coordinates = $value["coordinates"] ?? null;
        if(!isset($type) || !isset($coordinates) ) { 
            $fail("Mising type or coordinates field!");
        }
        
        if($type !== "Point") {
            $fail("type of display_point must be member with Point value.");
        }
        
        $pattern = '/^\[-?\d+(\.\d+)?,\d+(\.\d+)?\]$/';
        if(preg_match($pattern, json_encode($coordinates)) === 0) {
            $fail('invalid Point coordinate');
        }

        


    }
}
