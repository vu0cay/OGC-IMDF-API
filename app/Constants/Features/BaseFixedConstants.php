<?php

namespace App\Constants\Features;

abstract class BaseFixedConstants {
    public static function getConstanst() {
        $reflectionClass = new \ReflectionClass(static::class);

        return $reflectionClass->getConstants();   
    }

    public static function getConstansAsString(): string
    {
        $cons = self::getConstanst();
        $txt = "";
        foreach($cons as  $key=>$value) {
            $txt .= "$value,";
        }
        $txt = rtrim($txt, ",");
        return $txt;
    }
}