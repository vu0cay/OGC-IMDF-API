<?php

namespace App\Rules;

use App\Rules\ValidateRuleCSV\LoadIso639LanguageCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateIso639 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filePath = __DIR__ . '/ValidateRuleCSV/iso_639-1.csv';
        $iso639 = LoadIso639LanguageCode::loadIso639($filePath);

        $language_tags = array_keys($value);

        
        foreach($language_tags as $key) {
            $check = false;
            foreach ($iso639 as $country) {
                if (
                    strtolower($country['2alpha']) === strtolower($key)
                ) {
                    $check = true;
                }
            }
            if(!$check) {
                $fail('Invalid ISO-639-1 language code');
                return;
            }
        }

    }
}
