<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitCategory extends Model
{
    protected $table = TablesName::UNIT_CATEGORIES;
    protected $guarded = [];

    public function units(): HasMany {
        return $this->hasMany(Unit::class, 'unit_category_id', 'id');
    }
}
