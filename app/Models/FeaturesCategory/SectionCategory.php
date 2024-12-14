<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class SectionCategory extends Model
{
    protected $table = TablesName::SECTION_CATEGORIES;
    protected $guarded = [];
}
