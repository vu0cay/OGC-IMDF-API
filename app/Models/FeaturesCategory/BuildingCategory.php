<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Building;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingCategory extends Model
{
    protected $table = TablesName::BUILDING_CATEGORIES;
    protected $guarded = [];

    public function buildings(): HasMany {
        return $this->hasMany(Building::class, 'building_category_id', 'id');
    }
}
