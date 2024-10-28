<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    protected $table = TablesName::FEATURES;
    protected $fillable = [
        'feature_id',
        'feature_type',
        'geometry'
    ];

    public function venues(): HasMany {
        return $this->hasMany(Venue::class, 'venue_id', 'feature_id');
    }
    public function addresses(): HasMany {
        return $this->hasMany(Address::class, 'address_id', 'feature_id');
    }
    public function buildings(): HasMany {
        return $this->hasMany(Building::class, 'building_id', 'feature_id');
    }
    public function footprints(): HasMany {
        return $this->hasMany(Footprint::class, 'footprint_id', 'feature_id');
    }

    public function levels(): HasMany {
        return $this->hasMany(Level::class, 'level_id', 'feature_id');
    }
    public function units(): HasMany {
        return $this->hasMany(Unit::class, 'unit_id', 'feature_id');
    }
    
}
