<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccessibilityCategory extends Model
{
    protected $table = TablesName::ACCESSIBILITY_CATEGORIES;
    protected $guarded = [];

    // public function buildings(): HasMany {
    //     return $this->hasMany(Building::class, 'building_category_id', 'id');
    // }
    public function units(): BelongsToMany {
        return $this->belongsToMany(Unit::class, TablesName::UNIT_ACCESSIBILITY, 
                                     'accessibility_id', 'unit_id', 
                                     'id', 'unit_id');
    }
}
