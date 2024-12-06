<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesRelation\AddressAnchor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anchor extends Model
{
    protected $table = TablesName::ANCHORS;

    protected $guarded = [];

    // test 
    public function featuretest(): HasOne {
        return $this->hasOne(FeatureTest::class, 'id', 'feature_id');
    }


    public function address(): HasOne
    {
        return $this->HasOne(AddressAnchor::class, 'anchor_id', 'anchor_id');
    }

    /////////////////////////////


    // unit
    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }
}
