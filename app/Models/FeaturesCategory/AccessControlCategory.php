<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use Illuminate\Database\Eloquent\Model;

class AccessControlCategory extends Model
{
    protected $table = TablesName::ACCESSCONTROLS;
    protected $guarded = [];
}
