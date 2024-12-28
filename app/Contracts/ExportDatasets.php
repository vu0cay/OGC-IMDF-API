<?php

namespace App\Contracts;
use App\Models\Features\Feature;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportDatasets
{

    public static function exportRoutesToJsonZip()
    {
        // Directory to store temporary JSON files
        $tempDir = storage_path('app/Exports');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Get all routes
        $routes = Route::getRoutes();
        $files = [];
        
        $feature_api_routes = [];

        $features = Feature::all('feature_type');
        // dd($features);
        foreach ($features as $feature) { 
            if (substr($feature->feature_type, -1) === 'y') { 
                $str = 'api/'.substr($feature->feature_type, 0, -1).'ies';
            }  else if (substr($feature->feature_type, -1) === 's') { 
                $str = 'api/'.$feature->feature_type.'es';
            } else $str = 'api/'.$feature->feature_type.'s';

            array_push($feature_api_routes, $str);
        }
        // dd($feature_api_routes);

        foreach ($routes as $route) {
            $uri = $route->uri();
            $method = $route->methods()[0]; // Get the first HTTP method (GET, POST, etc.)
            
            // Skip routes without closures or controllers
            if (!$route->getAction('uses') || $method !== 'GET') {
                continue;
            }
            
            if(!in_array($uri, $feature_api_routes)) {
                continue;
            }  
            // Make a request to the route
            try {
                $response = app()->handle(request()->create($uri, $method));
                
                if ($response->getStatusCode() === 200) {
                    $content = $response->getContent();
                    // dd($content);
                    // Save response to a file
                    // $filename = str_replace(['/', ':'], '_', substr($uri,3)) . '.json';
                    $filename = substr($uri,4) . '.json';
                    $filePath = $tempDir . '/' . $filename;
                    file_put_contents($filePath, $content);
                    $files[] = $filePath;
                }
            } catch (\Exception $e) {
                // Handle errors, if needed
                continue;
            }
        }
        
        // Create ZIP file
        $zipPath = storage_path('app/routes_export.zip');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temporary files
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

}