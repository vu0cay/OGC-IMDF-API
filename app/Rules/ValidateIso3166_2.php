<?php

namespace App\Rules;

use App\Rules\ValidateRuleCSV\LoadIsoCountryCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateIso3166_2 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // dd($value);
        // $filePath = __DIR__ . '/ValidateRuleCSV/ISO-3166-2.csv';
        $filePath = __DIR__ . '/ValidateRuleCSV/subdivisions.csv';
        $iso3166 = LoadIsoCountryCode::loadIso3166_2($filePath);
        
        // $str = explode('-', $value);
        
        // if(count($str) != 2) { 
        //     $fail('Invalid ISO-3166-2 country code');
        //     return;
        // }

        // $country_code = $str[0];
        // $regional_code = $str[1];
        // dd($iso3166);

        foreach ($iso3166 as $country) {
            if (
                strtolower($country['regional_code']) === strtolower($value)
            ) {
                return;
            }
        }

        $fail('Invalid ISO-3166-2 country code');
    }
}
