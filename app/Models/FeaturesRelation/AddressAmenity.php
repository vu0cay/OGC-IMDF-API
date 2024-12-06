<?php

namespace App\Models\FeaturesRelation;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Features\TablesName;

class AddressAmenity extends Model
{
    protected $table = TablesName::ADDRESS_AMENITIES;
    protected $guarded = [];
}
