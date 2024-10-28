<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\AccessibilityCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesCategory\UnitCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    protected $table = TablesName::UNITS;

    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'unit_id');
    }
    public function category(): HasOne {
        return $this->hasOne(UnitCategory::class, 'id', 'unit_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }
        
    // this is just a trick to call eager load labels
    // because the actual label and footprint have a (0,1) and (0,n) relationship
    // the (0,1) week function dependant will change into to an entity that receive these 2 key as primary key
    // footprint_label (label_id, footprint_id) 
    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, TablesName::FEATURE_LABEL,foreignPivotKey: 'feature_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'unit_id', relatedKey: 'id');
    }

    public function accessibilities(): BelongsToMany {
        return $this->belongsToMany(AccessibilityCategory::class, TablesName::UNIT_ACCESSIBILITY, 
                                     'unit_id', 'accessibility_id', 
                                     'unit_id', 'id');
    }
}
