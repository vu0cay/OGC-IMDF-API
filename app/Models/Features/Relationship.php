<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\FeatureReference;
use App\Models\FeaturesCategory\RelationshipCategory;
use App\Models\FeaturesRelation\FeatureDestinationRelationship;
use App\Models\FeaturesRelation\FeatureIntermediaryRelationship;
use App\Models\FeaturesRelation\FeatureOriginRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Relationship extends Model
{
    protected $table = TablesName::RELATIONSHIPS;

    protected $guarded = [];

    public function category(): HasOne
    {
        return $this->hasOne(RelationshipCategory::class, 'id', 'relationship_category_id');
    }

    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }
    public function feature_origin(): HasOne
    {
        return $this->hasOne(FeatureReference::class, 'id', 'feature_type_id');
    }
    public function feature_intermerdiary(): HasOne
    {
        return $this->hasOne(FeatureReference::class, 'id', 'feature_type_id');
    }
    public function feature_destination(): HasOne
    {
        return $this->hasOne(FeatureReference::class, 'id', 'feature_type_id');
    }

    public function origin(): HasOne
    {
        return $this->HasOne(FeatureOriginRelationship::class, 'relationship_id', 'relationship_id');
    }
    public function intermediary(): BelongsToMany
    {
        return $this->belongsToMany(
            FeatureReference::class,
            TablesName::FEATURE_INTERMEDIARY_RELATIONSHIPS,
            'relationship_id',
            'feature_reference_id',
            'relationship_id',
            'id'
        );
    }


    public function destination(): HasOne
    {
        return $this->HasOne(FeatureDestinationRelationship::class, 'relationship_id', 'relationship_id');
    }
}
