<?php

namespace App\Models\Features;

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\AccessibilityCategory;
use App\Models\FeaturesCategory\Label;
use App\Models\FeaturesCategory\RestrictionCategory;
use App\Models\FeaturesCategory\SectionCategory;
use App\Models\FeaturesRelation\AddressSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Session;

class Section extends Model
{
    protected $table = TablesName::SECTIONS;

    protected $guarded = [];
    public function category(): HasOne
    {
        return $this->hasOne(SectionCategory::class, 'id', 'section_category_id');
    }
    public function restriction(): HasOne
    {
        return $this->hasOne(RestrictionCategory::class, 'id', 'restriction_category_id');
    }
    public function feature(): HasOne
    {
        return $this->hasOne(Feature::class, 'id', 'feature_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(
            Label::class,
            TablesName::SECTION_LABELS,
            foreignPivotKey: 'section_id',
            relatedPivotKey: 'label_id',
            parentKey: 'section_id',
            relatedKey: 'id'
        )->withPivot('type');
    }

    public function address(): HasOne
    {
        return $this->HasOne(AddressSection::class, 'section_id', 'section_id');
    }

    public function accessibilities(): BelongsToMany
    {
        return $this->belongsToMany(
            AccessibilityCategory::class,
            TablesName::SECTION_ACCESSIBILITY,
            'section_id',
            'accessibility_id',
            'section_id',
            'id'
        );
    }


    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            Section::class,
            TablesName::SECTION_PARENTS,
            'section_id',
            'parent_section_id',
            'section_id',
            'section_id'
        );
    }
}
