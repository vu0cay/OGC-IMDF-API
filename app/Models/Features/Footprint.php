<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Footprint extends Model
{
    protected $table = TablesName::FOOTPRINTS;

    protected $guarded = [];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'footprint_id');
    }
    
    public function buildings(): BelongsToMany {
        return $this->belongsToMany(Building::class, TablesName::FOOTPRINT_BUILDING,foreignPivotKey: 'footprint_id', relatedPivotKey: 'building_id', 
                                        parentKey: 'footprint_id', relatedKey: 'building_id');
    }


    
    // this is just a trick to call eager load labels
    // because the actual label and footprint have a (0,1) and (0,n) relationship
    // the (0,1) week function dependant will change into to an entity that receive these 2 key as primary key
    // footprint_label (label_id, footprint_id) 
    
    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, TablesName::FEATURE_LABEL,foreignPivotKey: 'feature_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'footprint_id', relatedKey: 'id');
    }

}
