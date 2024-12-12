<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\AccessControlCategory;
use App\Models\FeaturesCategory\AccessibilityCategory;
use App\Models\FeaturesCategory\Door;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\OpeningCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Opening extends Model
{
    protected $table = TablesName::OPENINGS;
    protected $guarded = [];

    public function category(): HasOne
    {
        return $this->hasOne(OpeningCategory::class, 'id', 'opening_category_id');
    }
    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::OPENING_LABELS,
            foreignPivotKey: 'opening_id',
            relatedPivotKey: 'label_id',
            parentKey: 'opening_id',
            relatedKey: 'id'
        )->withPivot('type');
    }

    public function accessibilities(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityCategory::class,
            TablesName::OPENING_ACCESSIBILITY,
            'opening_id',
            'accessibility_id',
            'opening_id',
            'id'
        );
    }

    public function accesscontrols(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessControlCategory::class,
            TablesName::OPENING_ACCESSCONTROL,
            'opening_id',
            'access_control_id',
            'opening_id',
            'id'
        );
    }

    public function door(): HasOne {
        return $this->hasOne(Door::class,'id', 'door_id');
    }
}
