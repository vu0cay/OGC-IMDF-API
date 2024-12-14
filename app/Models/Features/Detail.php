<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Detail extends Model
{
    protected $table = TablesName::DETAILS;

    protected $guarded = [];

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function level(): BelongsTo {
        return $this->belongsTo(Level::class,'level_id', 'level_id');
    }
}
