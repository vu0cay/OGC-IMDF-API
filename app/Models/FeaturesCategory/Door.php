<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class Door extends Model
{
    protected $table = TablesName::DOORS;
    protected $guarded = [];
}
