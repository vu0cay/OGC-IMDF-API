<?php

namespace App\Models\FeaturesCategory;

use App\Constants\Features\TablesName;
use App\Models\Features\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Label extends Model
{
    protected $table = TablesName::LABELS;
    protected $primaryKey = 'label_id';

    public $incrementing = true;

    protected $guarded = [];

    

}
