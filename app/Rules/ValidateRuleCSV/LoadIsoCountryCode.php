<?php

namespace App\Rules\ValidateRuleCSV;

//This site or product includes Ipregistry ISO 3166 data available from https://ipregistry.co.

class LoadIsoCountryCode 
{
    static function loadIso3166($path) {
        $iso3166 = [];
        if (($handle = fopen($path, "r")) !== false) {
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $iso3166[] = [
                    'alpha2' => $row[9]
                ];
                // $iso3166[] = [
                //     'alpha2' => $row[1], // Adjust index based on CSV structure
                //     'alpha3' => $row[2],
                //     'name' => $row[0]
                // ];
            }
            fclose($handle);
        }
        return $iso3166;
    }
    static function loadIso3166_2($path) {
        $iso31662 = [];
        if (($handle = fopen($path, "r")) !== false) {
            $header = fgetcsv($handle); 
            while (($row = fgetcsv($handle)) !== false) {
                // dd($row);
                // $str = str_replace('"','',$row[0]);
                // $arr = explode(';', $str);
                // dd($arr);
                // if($arr[0] === 'VN') {
                //     dd($arr);
                // }
                $iso31662[] = [
                    'alpha2' => $row[0],
                    'regional_code' => $row[1],
                    'region_name' => $row[2],
                ];
            }
            fclose($handle);
        }
        return $iso31662;
    }
}
