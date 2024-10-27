<?php

namespace App\Models\FeaturesCategory;

use Illuminate\Database\Eloquent\Model;

class BuildingLabel extends Model
{
    protected $table = TablesName::BUILDING_LABEL;
    protected $primaryKey = ['venue_id', 'label_id'];

    
}
