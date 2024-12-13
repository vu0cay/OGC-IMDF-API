<?php

namespace App\Models\FeaturesRelation;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class FeatureOriginRelationship extends Model
{
    protected $table = TablesName::FEATURE_ORIGIN_RELATIONSHIPS;
    protected $guarded = []; 
}
