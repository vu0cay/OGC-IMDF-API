<?php

namespace App\Rules\Opening;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateDoor implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        
        $type_value = [
            "movablepartition",
            "open",
            "revolving",
            "shutter",
            "sliding",
            "swinging",
            "turnstile",
            "turnstile.fullheight",
            "turnstile.waistheight"
        ];

        $material = [
            "wood",
            "glass",
            "metal",
            "gate",
        ];

        foreach ($value as $key => $val) {
            switch ($key) {
                case "type":
                    if (!in_array($val, $type_value)) {
                        $fail('invalid type of door');
                        return;
                    }

                    break;
                case "material":
                    if (!in_array($val, $material)) {
                        $fail('invalid material of door');
                        return;
                    }
                    break;
                case "automatic":
                    if (!is_bool($val)) {
                        $fail('automatic must be bool value');
                        return;
                    }
                    break;
                default:
                    $fail('something wrong when adding door data!');
                    return;
            }

        }
    }
}
