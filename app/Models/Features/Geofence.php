<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\GeofenceCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Geofence extends Model
{
    protected $table = TablesName::GEOFENCES;
    protected $guarded = [];

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::GEOFENCE_LABELS,
            foreignPivotKey: 'geofence_id',
            relatedPivotKey: 'label_id',
            parentKey: 'geofence_id',
            relatedKey: 'id'
        )->withPivot('type');
    }
    public function category(): HasOne
    {
        return $this->hasOne(GeofenceCategory::class, 'id', 'geofence_category_id');
    }
    public function restriction(): HasOne
    {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    public function buildings(): BelongsToMany
    {
        return $this->belongsToMany(
            Building::class,
            TablesName::GEOFENCE_BUILDING,
            foreignPivotKey: 'geofence_id',
            relatedPivotKey: 'building_id',
            parentKey: 'geofence_id',
            relatedKey: 'building_id'
        );
    }
    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(
            Level::class,
            TablesName::GEOFENCE_LEVEL,
            foreignPivotKey: 'geofence_id',
            relatedPivotKey: 'level_id',
            parentKey: 'geofence_id',
            relatedKey: 'level_id'
        );
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            Geofence::class,
            TablesName::GEOFENCE_PARENTS,
            'geofence_id',
            'parent_geofence_id',
            'geofence_id',
            'geofence_id'
        );
    }
}
