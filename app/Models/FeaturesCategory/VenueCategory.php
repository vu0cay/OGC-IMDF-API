<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueCategory extends Model
{
    protected $table = TablesName::VENUE_CATEGORIES;

    public function venues(): HasMany {
        return $this->hasMany(Venue::class, 'venue_category_id', 'id');
    }
    
}
