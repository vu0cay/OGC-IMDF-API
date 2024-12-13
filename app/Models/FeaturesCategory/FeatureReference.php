<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FeatureReference extends Model
{
    protected $guarded = [];
    protected $table = TablesName::FEATURE_REFERENCES;


    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_type_id');
    }
    
}
