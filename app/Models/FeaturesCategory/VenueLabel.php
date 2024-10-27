<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class VenueLabel extends Model
{
    protected $table = TablesName::VENUE_LABEL;
    protected $primaryKey = ['venue_id', 'label_id'];
    
}
