<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anchor extends Model
{
    protected $table = TablesName::ANCHORS;

    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'anchor_id');
    }
}
