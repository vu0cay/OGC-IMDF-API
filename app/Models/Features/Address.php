<?php

namespace App\Models\Features;
use App\Constants\Features\TablesName;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    protected $table =  TablesName::ADDRESSES;
    protected $guarded = [];


    // test
    public function featuretest(): HasOne {
        return $this->hasOne(FeatureTest::class, 'id', 'feature_id');
    }
    //
    
    public function venues(): HasMany {
        return $this->hasMany(Venue::class, 'address_id', 'address_id');
    }
    public function buildings(): HasMany {
        return $this->hasMany(Building::class, 'address_id', 'address_id');
    }

    public function anchors(): HasMany {
        return $this->hasMany(Anchor::class, 'address_id', 'address_id');
    }

    public function amenities(): BelongsToMany {
        return $this->belongsToMany(Amenity::class, 'address_amenities', 
                                    'address_id', 'amenity_id',
                                            'address_id', 'amenity_id');
    }
}
