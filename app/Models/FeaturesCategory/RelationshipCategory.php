<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class RelationshipCategory extends Model
{
    protected $table = TablesName::RELATIONSHIP_CATEGORIES;
    protected $guarded = [];
}
