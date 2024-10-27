<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Level;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelCategory extends Model
{
    protected $table = TablesName::LEVEL_CATEGORIES;
    protected $guarded = [];

    public function levels(): HasMany {
        return $this->hasMany(Level::class, 'level_category_id', 'id');
    }
}
