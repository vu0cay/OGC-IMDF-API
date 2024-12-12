<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kiosk extends Model
{
    protected $table = TablesName::KIOSKS;
    protected $guarded = [];

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::KIOSK_LABELS,
            foreignPivotKey: 'kiosk_id',
            relatedPivotKey: 'label_id',
            parentKey: 'kiosk_id',
            relatedKey: 'id'
        )->withPivot('type');
    }
    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }
}
