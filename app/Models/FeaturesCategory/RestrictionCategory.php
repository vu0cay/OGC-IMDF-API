<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Building;
use App\Models\Features\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestrictionCategory extends Model
{
    protected $table = TablesName::RESTRICTION_CATEGORIES;

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'restriction_category_id');
    }
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'restriction_category_id');
    }
    
}
