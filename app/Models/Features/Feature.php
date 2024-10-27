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

}
