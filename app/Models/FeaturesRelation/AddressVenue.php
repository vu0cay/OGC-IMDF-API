<?php

namespace App\Models\FeaturesRelation;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Features\TablesName;

class AddressVenue extends Model
{
    protected $table = TablesName::ADDRESS_VENUES;
    protected $guarded = []; 
}
