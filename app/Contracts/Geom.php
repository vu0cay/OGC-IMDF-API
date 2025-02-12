<?php

namespace App\Contracts;

use geoPHP;
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
    public static function GeomFromText($geojson) : string
    {
        // ST_GeomFromText('POINT(10 10)', 4326)
        if($geojson === null) return "null";

        // Convert GeoJSON to Geometry Object
        $geometry = geoPHP::load(json_encode($geojson), 'json');
        
        // Convert Geometry to WKT
        $wkt = $geometry->out('wkt');
        
        return "ST_GeomFromText('$wkt', 4326)";
        
            
    }
}
