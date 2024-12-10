<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\FootprintCategory;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Footprint extends Model
{
    protected $table = TablesName::FOOTPRINTS;

    protected $guarded = [];


    public function buildings(): BelongsToMany
    {
        return $this->belongsToMany(
            Building::class,
            TablesName::FOOTPRINT_BUILDING,
            foreignPivotKey: 'footprint_id',
            relatedPivotKey: 'building_id',
            parentKey: 'footprint_id',
            relatedKey: 'building_id'
        );
    }

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            'footprint_labels',
            foreignPivotKey: 'footprint_id',
            relatedPivotKey: 'label_id',
            parentKey: 'footprint_id',
            relatedKey: 'id'
        )->withPivot('type');
    }

    public function category(): HasOne
    {
        return $this->hasOne(FootprintCategory::class, 'id', 'footprint_category_id');
    }

}
