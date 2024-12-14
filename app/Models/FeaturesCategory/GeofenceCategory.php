<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class GeofenceCategory extends Model
{
    protected $table = TablesName::GEOFENCE_CATEGORIES;
    protected $guarded = [];

    // public function footprints(): HasMany {
    //     return $this->hasMany(Footprint::class, 'footprint_category_id', 'id');
    // }
}
