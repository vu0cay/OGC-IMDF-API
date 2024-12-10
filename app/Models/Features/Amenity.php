<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\AccessibilityCategory;
use App\Models\FeaturesCategory\AmenityCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesRelation\AddressAmenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Amenity extends Model
{
    protected $table = TablesName::AMENITIES;

    protected $guarded = [];

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::AMENTITY_LABEL,
            foreignPivotKey: 'amenity_id',
            relatedPivotKey: 'label_id',
            parentKey: 'amenity_id',
            relatedKey: 'id'
        )->withPivot('type');
    }
    public function address(): HasOne
    {
        return $this->HasOne(AddressAmenity::class, 'amenity_id', 'amenity_id');
    }
    public function category(): HasOne
    {
        return $this->hasOne(AmenityCategory::class, 'id', 'amenity_category_id');
    }
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }

    public function accessibilities(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityCategory::class,
            TablesName::AMENITY_ACCESSIBILITY,
            'amenity_id',
            'accessibility_id',
            'amenity_id',
            'id'
        );
    }
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(
            Unit::class,
            TablesName::AMENITY_UNIT,
            foreignPivotKey: 'amenity_id',
            relatedPivotKey: 'unit_id',
            parentKey: 'amenity_id',
            relatedKey: 'unit_id',
        );
    }
}
