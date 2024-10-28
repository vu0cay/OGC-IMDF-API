<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anchor extends Model
{
    protected $table = TablesName::ANCHORS;

    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'anchor_id');
    }


    // address
    // unit
    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class, 'unit_id', 'unit_id');
    }
    public function address(): BelongsTo {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
}
