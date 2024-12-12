<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\OccupantCategory;
use App\Models\FeaturesCategory\Validity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Occupant extends Model
{
    protected $table = TablesName::OCCUPANTS;

    protected $guarded = [];

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::OCCUPANT_LABELS,
            foreignPivotKey: 'occupant_id',
            relatedPivotKey: 'label_id',
            parentKey: 'occupant_id',
            relatedKey: 'id'
        )->withPivot('type');
    }


    public function category(): HasOne
    {
        return $this->hasOne(OccupantCategory::class, 'id', 'occupant_category_id');
    }
    public function validity(): HasOne {
        return $this->hasOne(Validity::class,'id', 'validity_id');
    }
    
}
