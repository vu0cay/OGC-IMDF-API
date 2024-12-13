<?php

namespace App\Models\FeaturesRelation;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class FeatureDestinationRelationship extends Model
{
    protected $table = TablesName::FEATURE_DESTINATION_RELATIONSHIPS;
    protected $guarded = []; 
}
