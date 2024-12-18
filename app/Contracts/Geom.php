<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geom extends Model
{
    use HasFactory;
    /**
     * Summary of GeomFromText
     * This is a function return a string value for DB:raw(value) to store geometry field
     * Use 1: 
     * "geometry": {
     *      "type": "Point",
     *      "coordinates": [10, 10]
     * }
     * => (string) ST_GeomFromText('Point(10 10)',4326)
     * Use 2: 
     * "geometry": {
     *      "type": "Polygon",
     *      "coordinates": [
     *          [10, 10],
     *          [11, 11],
     *          [12, 12]
     *      ]
     * }
     * => (string) ST_GeomFromText('Polygon((10 10, 11 11, 12 12))',4326)
     * @param mixed $geometry
     * @return string
     */
    public static function GeomFromText($geometry) : string
    {
        // ST_GeomFromText('POINT(10 10)', 4326)
        if($geometry === null) return "null";

        switch($geometry["type"]) {
            case "Point":         
                $x = $geometry["coordinates"][0];
                $y = $geometry["coordinates"][1];
                return "ST_GeomFromText('POINT($x". " "."$y)', 4326)";
                
            case "Polygon":
                $coordinates = $geometry["coordinates"][0];
                $text = "ST_GeomFromText('POLYGON((";
                foreach($coordinates as $coordinate) {
                    $text .= $coordinate[0]." ".$coordinate[1].", ";
                }
                $text = rtrim($text, ', ')."))', 4326)";
                return $text;
            case "MultiPolygon":
                    $polygons = $geometry["coordinates"];
                    //"MULTIPOLYGON(((180.0 40.0, 180.0 50.0, 170.0 50.0, 170.0 40.0, 180.0 40.0)),
                    //((-170.0 40.0, -170.0 50.0, -180.0 50.0, -180.0 40.0, -170.0 40.0))
                    //)"
                    $text = "ST_GeomFromText('MULTIPOLYGON(";
                    foreach($polygons as $polygon) {
                        $coordinates = $polygon[0];
                        $text .= '((';
                        foreach($coordinates as $coordinate) {
                            $text .= $coordinate[0]." ".$coordinate[1].", ";
                        }
                        $text = rtrim($text, ", ");
                        $text .= ')),';
                    }
                    $text = rtrim($text, ', ').")', 4326)";
                    
                    return $text;
            case "LineString":
                $coordinates = $geometry["coordinates"];
                $text = "ST_GeomFromText('LineString(";
                foreach($coordinates as $coordinate) {
                    $text .= $coordinate[0]." ".$coordinate[1].", ";
                }
                $text = rtrim($text, ', ').")', 4326)";
                return $text;
            case "MultiLineString":
                    $lineStrings = $geometry["coordinates"];
                    $text = "ST_GeomFromText('MultiLineString(";
                    foreach($lineStrings as $linestring) { 
                        $text .= '(';
                        foreach($linestring as $coordinate) {
                            // dd($coordinate);
                            $text .= $coordinate[0]." ".$coordinate[1].", ";
                         }
                        $text = rtrim($text, ", ");
                        $text .= '),';
                        // dd($text);

                    }
                    $text = rtrim($text, ', ').")', 4326)";
                    // dd($text);
                    return $text;

                   
            default: 
                return "null"; 
            }
            
    }
}
