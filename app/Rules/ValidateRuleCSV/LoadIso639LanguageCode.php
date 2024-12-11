<?php

namespace App\Rules\ValidateRuleCSV;

//This site or product includes Ipregistry ISO 3166 data available from https://ipregistry.co.

class LoadIso639LanguageCode 
{
    static function loadIso639($path) {
        $iso639 = [];
        if (($handle = fopen($path, "r")) !== false) {
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $iso639[] = [
                    '2alpha' => $row[3]
                ];
            }
            fclose($handle);
        }
        // dd($iso639);
        return $iso639;
    }
}
