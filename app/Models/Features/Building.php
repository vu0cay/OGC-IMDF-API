<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\BuildingCategory;
use App\Models\FeaturesCategory\RestrictionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Building extends Model
{
    protected $table = TablesName::BUILDINGS;
    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'building_id');
    }
    public function category(): HasOne {
        return $this->hasOne(BuildingCategory::class, 'id', 'building_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    public function address(): HasOne {
        return $this->hasOne(Address::class, 'address_id', 'address_id');
    }
}
