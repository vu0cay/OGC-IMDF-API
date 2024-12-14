<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\FixtureCategory;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fixture extends Model
{
    
    protected $table = TablesName::FIXTURES;

    protected $guarded = [];
    public function category(): HasOne
    {
        return $this->hasOne(FixtureCategory::class, 'id', 'fixture_category_id');
    }
    
    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::FIXTURE_LABELS,
            foreignPivotKey: 'fixture_id',
            relatedPivotKey: 'label_id',
            parentKey: 'fixture_id',
            relatedKey: 'id'
        )->withPivot('type');
    }

    // public function level(): BelongsTo {
    //     return $this->belongsTo(Level::class,'level_id', 'level_id');
    // }
}
