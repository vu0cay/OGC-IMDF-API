<?php

namespace App\Models\FeaturesRelation;

use Illuminate\Database\Eloquent\Model;
use App\Constants\Features\TablesName;

class AddressAnchor extends Model
{
    protected $table = TablesName::ADDRESS_ANCHORS;
    protected $guarded = [];
}
