<?php

namespace App\Models\FeaturesRelation;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Features\TablesName;

class AddressLevel extends Model
{
    protected $table = TablesName::ADDRESS_LEVELS;
    protected $guarded = []; 
}
