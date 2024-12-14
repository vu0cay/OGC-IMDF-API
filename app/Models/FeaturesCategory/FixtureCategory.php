<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class FixtureCategory extends Model
{
    protected $table = TablesName::FIXTURE_CATEGORIES;
    protected $guarded = [];
}
