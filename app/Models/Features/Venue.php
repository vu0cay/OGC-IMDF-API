<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesCategory\VenueCategory;
use App\Models\FeaturesCategory\VenueLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Venue extends Model
{
    protected $table = TablesName::VENUES;

    protected $fillable = [
        'venue_id',
        'venue_category_id',
        'restriction_category_id',
        'hours',
        'phone',
        'website',
        'display_point'
    ];

    public function feature(): HasOne {
        return $this->hasOne(Feature::class, 'feature_id', 'venue_id');
    }
    public function category(): HasOne {
        return $this->hasOne(VenueCategory::class, 'id', 'venue_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

    public function address(): HasOne {
        return $this->hasOne(Address::class, 'address_id', 'address_id');
    }
    

    // this is just a trick to call eager load labels
    // because the actual label and footprint have a (0,1) and (0,n) relationship
    // the (0,1) week function dependant will change into to an entity that receive these 2 key as primary key
    // footprint_label (label_id, footprint_id) 
    
    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, TablesName::FEATURE_LABEL, foreignPivotKey: 'feature_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'venue_id', relatedKey: 'id');
    }
}
