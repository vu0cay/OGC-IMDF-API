<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class Validity extends Model
{
    protected $table = TablesName::VALIDITIES;
    protected $guarded = [];
}
