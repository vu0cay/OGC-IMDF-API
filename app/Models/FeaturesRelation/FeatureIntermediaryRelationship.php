<?php

namespace App\Models\FeaturesRelation;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class FeatureIntermediaryRelationship extends Model
{
    protected $table = TablesName::FEATURE_INTERMEDIARY_RELATIONSHIPS;
    protected $guarded = []; 
}
