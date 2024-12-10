<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\BuildingCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesRelation\AddressBuilding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Building extends Model
{
    protected $table = TablesName::BUILDINGS;
    protected $guarded = [];
    public function category(): HasOne
    {
        return $this->hasOne(BuildingCategory::class, 'id', 'building_category_id');
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
            'building_labels',
            foreignPivotKey: 'building_id',
            relatedPivotKey: 'label_id',
            parentKey: 'building_id',
            relatedKey: 'id'
        )->withPivot('type');
    }

    public function address(): HasOne
    {
        return $this->HasOne(AddressBuilding::class, 'building_id', 'building_id');
    }

    public function footprints(): BelongsToMany
    {
        return $this->belongsToMany(
            Footprint::class,
            TablesName::FOOTPRINT_BUILDING,
            'building_id',
            'footprint_id',
            'building_id',
            'footprint_id'
        );
    }
    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(
            Level::class,
            TablesName::LEVEL_BUILDING,
            'building_id',
            'level_id',
            'building_id',
            'level_id'
        );
    }

}
