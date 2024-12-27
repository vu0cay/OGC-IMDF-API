<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

function exportRoutesToJsonZip()
{
    // Directory to store temporary JSON files
    $tempDir = storage_path('app/temp_routes');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Get all routes
    $routes = Route::getRoutes();
    $files = [];

    foreach ($routes as $route) {
        $uri = $route->uri();
        $method = $route->methods()[0]; // Get the first HTTP method (GET, POST, etc.)
        
        // Skip routes without closures or controllers
        if (!$route->getAction('uses') || $method !== 'GET') {
            continue;
        }

        // Make a request to the route
        try {
            $response = app()->handle(request()->create($uri, $method));
            if ($response->getStatusCode() === 200) {
                $content = $response->getContent();
                
                // Save response to a file
                $filename = str_replace(['/', ':'], '_', $uri) . '.json';
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
