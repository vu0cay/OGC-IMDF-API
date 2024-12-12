<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class OpeningCategory extends Model
{
    protected $table = TablesName::OPENING_CATEGORIES;
    protected $guarded = [];

    
}
