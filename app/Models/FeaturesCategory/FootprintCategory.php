<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Footprint;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FootprintCategory extends Model
{
    protected $table = TablesName::FOOTPRINT_CATEGORIES;
    protected $guarded = [];

    public function footprints(): HasMany {
        return $this->hasMany(Footprint::class, 'footprint_category_id', 'id');
    }
}
