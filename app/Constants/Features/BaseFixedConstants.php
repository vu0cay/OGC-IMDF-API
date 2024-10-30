<?php

namespace App\Constants\Features;

abstract class BaseFixedConstants {
    public static function getConstanst() {
        $reflectionClass = new \ReflectionClass(static::class);

        return $reflectionClass->getConstants();
        
    }
}