<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class OccupantCategory extends Model
{
    protected $table = TablesName::OCCUPANT_CATEGORIES;
    protected $guarded = [];
}
