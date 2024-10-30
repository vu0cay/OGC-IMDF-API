<?php

namespace App\Constants\Features\Category;

use App\Constants\Features\BaseFixedConstants;
use Faker\Provider\Base;

final class BuildingCategory extends BaseFixedConstants {
    const PARKING = 'parking';
    const TRANSIT = 'transit';
    const TRANSIT_BUS = 'transit.bus';
    const TRANSIT_TRAIN = 'transit.train';
    const UNSPECIFIED = 'unspecified';
}