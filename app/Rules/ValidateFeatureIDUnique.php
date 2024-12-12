<?php

namespace App\Rules;

use App\Constants\Features\TablesName;
use Closure;
use DB;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateFeatureIDUnique implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $kiosk_ids = DB::table(TablesName::KIOSKS)->select('kiosk_id as fid');
        $anchor_ids = DB::table(TablesName::ANCHORS)->select('anchor_id as fid');
        $amenity_ids = DB::table(TablesName::AMENITIES)->select('amenity_id as fid');
        $unit_ids = DB::table(TablesName::UNITS)->select('unit_id as fid');
        $level_ids = DB::table(TablesName::LEVELS)->select('level_id as fid');
        $building_ids = DB::table(TablesName::BUILDINGS)->select('building_id as fid');
        $footprint_ids = DB::table(TablesName::FOOTPRINTS)->select('footprint_id as fid');
        $address_ids = DB::table(TablesName::ADDRESSES)->select('address_id as fid');
        $venue_ids = DB::table(TablesName::VENUES)->select('venue_id as fid');
        
        $combined = $unit_ids
            ->union($kiosk_ids)
            ->union($anchor_ids)
            ->union($amenity_ids)
            ->union($level_ids)
            ->union($building_ids)
            ->union($footprint_ids)
            ->union($venue_ids)
            ->union($address_ids)
            ->get();
        
        
            $combined->map(function ($f) use($value, $fail) { 
                if($value === $f->fid) {
                    $fail('Feature-ID must be a globally unique.');
                    return;
            }
        } );
    }
}
