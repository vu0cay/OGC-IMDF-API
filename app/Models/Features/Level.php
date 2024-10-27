<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\LevelCategory;
use App\Models\FeaturesCategory\RestrictionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    protected $table = TablesName::LEVELS;
    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'level_id');
    }
    public function category(): HasOne {
        return $this->hasOne(LevelCategory::class, 'id', 'level_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    public function address(): HasOne {
        return $this->hasOne(Address::class, 'address_id', 'address_id');
    }

    public function buildings(): BelongsToMany {
        return $this->belongsToMany(Building::class, TablesName::LEVEL_BUILDING,foreignPivotKey: 'level_id', relatedPivotKey: 'building_id', 
                                        parentKey: 'level_id', relatedKey: 'building_id');
    }

     // this is just a trick to call eager load labels
    // because the actual label and footprint have a (0,1) and (0,n) relationship
    // the (0,1) week function dependant will change into to an entity that receive these 2 key as primary key
    // footprint_label (label_id, footprint_id) 
    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, TablesName::LEVEL_LABEL,foreignPivotKey: 'level_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'level_id', relatedKey: 'id');
    }
}
