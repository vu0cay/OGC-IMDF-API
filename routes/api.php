<?php

use App\Constants\Features\TablesName;
use App\Http\Controllers\Features\BuildingController;
use App\Http\Controllers\Features\FootprintController;
use App\Http\Controllers\Features\LevelController;
use App\Http\Controllers\Features\VenueController;
use App\Models\Features\Footprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::apiResource("units", UnitController::class);
// Route::apiResource("anchors", AnchorController::class);
// Route::apiResource("levels", LevelController::class);
// Route::apiResource("amenities", AmenityController::class);
// Route::apiResource("buildings", BuildingController::class);
// Route::apiResource("addresses", AddressController::class);
// Route::apiResource("footprints", FootprintController::class);
// Route::apiResource("openings", OpeningController::class);
Route::apiResource(TablesName::VENUES, VenueController::class);

Route::controller(VenueController::class)->group(function () {

    Route::get('/venues', 'index');
    Route::get('/venues/{venue_id}', 'show');

});
/*
    Address Controller
*/

Route::controller(BuildingController::class)->group(function () {

    Route::get('/buildings', 'index');
    Route::get('/buildings/{building_id}', 'show');

});
Route::controller(FootprintController::class)->group(function () {

    Route::get('/footprints', 'index');
    Route::get('/footprints/{footprint_id}', 'show');

});

Route::controller(LevelController::class)->group(function () {

    Route::get('/levels', 'index');
    Route::get('/levels/{level_id}', 'show');

});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
