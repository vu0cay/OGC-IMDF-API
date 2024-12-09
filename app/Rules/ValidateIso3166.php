<?php

namespace App\Rules;

use App\Rules\ValidateRuleCSV\LoadIsoCountryCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateIso3166 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filePath = __DIR__ . '/ValidateRuleCSV/country-codes.csv';
        $iso3166 = LoadIsoCountryCode::loadIso3166($filePath);
        foreach ($iso3166 as $country) {

            if (
                strtolower($country['alpha2']) === strtolower($value)
                // || strtolower($country['alpha3']) === strtolower($value) ||
                // strtolower($country['name']) === strtolower($value)
            ) {
                return;
            }
        }

        $fail('Invalid ISO 3166 country code');
    }
}
