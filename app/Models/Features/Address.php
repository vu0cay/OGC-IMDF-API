<?php

namespace App\Models\Features;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $table = "addresses";
    protected $guarded = [];


    public function venues(): HasMany {
        return $this->hasMany(Venue::class, 'address_id', 'address_id');
    }
    public function buildings(): HasMany {
        return $this->hasMany(Building::class, 'address_id', 'address_id');
    }

    public function anchors(): HasMany {
        return $this->hasMany(Anchor::class, 'address_id', 'address_id');
    }
}
