<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\LevelCategory;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesRelation\AddressLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    protected $table = TablesName::LEVELS;
    protected $guarded = [];


    /// test
    
    public function featuretest(): HasOne {
        return $this->hasOne(FeatureTest::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, 'level_labels', 
                                        foreignPivotKey: 'level_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'level_id', relatedKey: 'id');
    }
    public function address(): HasOne
    {
        return $this->HasOne(AddressLevel::class, 'level_id', 'level_id');
    }

    ////

    public function category(): HasOne {
        return $this->hasOne(LevelCategory::class, 'id', 'level_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    public function buildings(): BelongsToMany {
        return $this->belongsToMany(Building::class, TablesName::LEVEL_BUILDING,foreignPivotKey: 'level_id', relatedPivotKey: 'building_id', 
                                        parentKey: 'level_id', relatedKey: 'building_id');
    }


}
