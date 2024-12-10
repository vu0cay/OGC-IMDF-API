<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\AccessibilityCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesCategory\UnitCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    protected $table = TablesName::UNITS;

    protected $guarded = [];
    public function category(): HasOne
    {
        return $this->hasOne(UnitCategory::class, 'id', 'unit_category_id');
    }
    public function restriction(): HasOne
    {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    
    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::UNIT_LABELS,
            foreignPivotKey: 'unit_id',
            relatedPivotKey: 'label_id',
            parentKey: 'unit_id',
            relatedKey: 'id'
        )->withPivot('type');
    }



    public function accessibilities(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityCategory::class,
            TablesName::UNIT_ACCESSIBILITY,
            'unit_id',
            'accessibility_id',
            'unit_id',
            'id'
        );
    }

    public function anchors(): HasMany
    {
        return $this->hasMany(Anchor::class, 'unit_id', 'unit_id');
    }


    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(
            Amenity::class,
            TablesName::AMENITY_UNIT,
            foreignPivotKey: 'unit_id',
            relatedPivotKey: 'amenity_id',
            parentKey: 'unit_id',
            relatedKey: 'amenity_id',
        );
    }

    public function level(): BelongsTo {
        return $this->belongsTo(Level::class,'level_id', 'level_id');
    }
}
