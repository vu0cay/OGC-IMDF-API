<?php

namespace App\Models\FeaturesRelation;
use App\Constants\Features\TablesName;

use Illuminate\Database\Eloquent\Model;

class AddressBuilding extends Model
{
    protected $table = TablesName::ADDRESS_BUILDINGS;
    protected $guarded = []; 
}
