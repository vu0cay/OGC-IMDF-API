<?php

namespace App\Contracts;


class GetFeatureName
{
    public static function getName($labels, $type)
    {

        $name = count($labels) > 0 ?
            $labels
                ->filter(function ($item) use ($type) {
                    return $item->pivot->type == $type;
                })
                ->pluck('value', 'language_tag')->toArray()
            : null;
        return $name;
    }
}