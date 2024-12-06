<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesCategory\VenueCategory;
use App\Models\FeaturesCategory\VenueLabel;
use App\Models\FeaturesRelation\AddressVenue;
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



     /// test
    
     public function featuretest(): HasOne {
        return $this->hasOne(FeatureTest::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany {
        return $this->belongsToMany(Label::class, 'venue_labels', 
                                        foreignPivotKey: 'venue_id', relatedPivotKey: 'label_id', 
                                        parentKey: 'venue_id', relatedKey: 'id');
    }
    public function address(): HasOne
    {
        return $this->HasOne(AddressVenue::class, 'venue_id', 'venue_id');
    }

    /////////////////////////////////


   
    public function category(): HasOne {
        return $this->hasOne(VenueCategory::class, 'id', 'venue_category_id');
    }
    public function restriction(): HasOne {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }

}
