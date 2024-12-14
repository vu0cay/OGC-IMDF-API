<?php

use App\Constants\Features\TablesName;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\Features\AddressController;
use App\Http\Controllers\Features\AmenityController;
use App\Http\Controllers\Features\AnchorController;
use App\Http\Controllers\Features\BuildingController;
use App\Http\Controllers\Features\FixtureController;
use App\Http\Controllers\Features\FootprintController;
use App\Http\Controllers\Features\KioskController;
use App\Http\Controllers\Features\LevelController;
use App\Http\Controllers\Features\OccupantController;
use App\Http\Controllers\Features\OpeningController;
use App\Http\Controllers\Features\RelationshipController;
use App\Http\Controllers\Features\UnitController;
use App\Http\Controllers\Features\VenueController;
use App\Http\Controllers\Functions\SearchController;

use App\Http\Controllers\SectionController;
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
// Route::apiResource(TablesName::VENUES, VenueController::class);

Route::controller(AddressController::class)->group(function () {

    Route::get('/addresses', 'index');
    Route::get('/addresses/{address_id}', 'show');
    Route::post('/addresses', 'store');
    Route::put('/addresses/{address_id}', 'update');
    Route::delete('/addresses/{address_id}', 'destroy');

});

Route::controller(VenueController::class)->group(function () {

    Route::get('/venues', 'index');
    Route::get('/venues/{venue_id}', 'show');
    Route::post('/venues', 'store');
    Route::put('/venues/{venue_id}', 'update');
    Route::delete('/venues/{venue_id}', 'destroy');

});

Route::controller(FootprintController::class)->group(function () {

    Route::get('/footprints', 'index');
    Route::get('/footprints/{footprint_id}', 'show');
    Route::post('/footprints', 'store');
    Route::put('/footprints/{footprint_id}', 'update');
    Route::delete('/footprints/{footprint_id}', 'destroy');

});

Route::controller(BuildingController::class)->group(function () {

    Route::get('/buildings', 'index');
    Route::get('/buildings/{building_id}', 'show');
    Route::post('/buildings', 'store');
    Route::put('/buildings/{building_id}', 'update');
    Route::delete('/buildings/{building_id}', 'destroy');

});

Route::controller(LevelController::class)->group(function () {

    Route::get('/levels', 'index');
    Route::get('/levels/{level_id}', 'show');
    Route::post('/levels', 'store');
    Route::put('/levels/{level_id}', 'update');
    Route::delete('/levels/{level_id}', 'destroy');

});

Route::controller(UnitController::class)->group(function () {

    Route::get('/units', 'index');
    Route::get('/units/{unit_id}', 'show');
    Route::post('/units', 'store');
    Route::put('/units/{unit_id}', 'update');
    Route::delete('/units/{unit_id}', 'destroy');

});

Route::controller(AnchorController::class)->group(function () {
    Route::get('/anchors', 'index');
    Route::get('/anchors/{anchor_id}', 'show');
    Route::post('/anchors', 'store');
    Route::put('/anchors/{anchor_id}', 'update');
    Route::delete('/anchors/{anchor_id}', 'destroy');
});

Route::controller(AmenityController::class)->group(function () {
    Route::get('/amenities', 'index');
    Route::get('/amenities/{amenity_id}', 'show');
    Route::post('/amenities', 'store');
    Route::put('/amenities/{amenity_id}', 'update');
    Route::delete('/amenities/{amenity_id}', 'destroy');
});

Route::controller(KioskController::class)->group(function () {
    Route::get('/kiosks', 'index');
    Route::get('/kiosks/{kiosk_id}', 'show');
    Route::post('/kiosks', 'store');
    Route::put('/kiosks/{kiosk_id}', 'update');
    Route::delete('/kiosks/{kiosk_id}', 'destroy');
});

Route::controller(OpeningController::class)->group(function () {
    Route::get('/openings', 'index');
    Route::get('/openings/{opening_id}', 'show');
    Route::post('/openings', 'store');
    Route::put('/openings/{opening_id}', 'update');
    Route::delete('/openings/{opening_id}', 'destroy');
});

Route::controller(OccupantController::class)->group(function () {
    Route::get('/occupants', 'index');
    Route::get('/occupants/{occupant_id}', 'show');
    Route::post('/occupants', 'store');
    Route::put('/occupants/{occupant_id}', 'update');
    Route::delete('/occupants/{occupant_id}', 'destroy');
});

Route::controller(RelationshipController::class)->group(function () {
    Route::get('/relationships', 'index');
    Route::get('/relationships/{relationship_id}', 'show');
    Route::post('/relationships', 'store');
    Route::put('/relationships/{relationship_id}', 'update');
    Route::delete('/relationships/{relationship_id}', 'destroy');
});

Route::controller(DetailController::class)->group(function () {
    Route::get('/details', 'index');
    Route::get('/details/{detail_id}', 'show');
    Route::post('/details', 'store');
    Route::put('/details/{detail_id}', 'update');
    Route::delete('/details/{detail_id}', 'destroy');
});

Route::controller(FixtureController::class)->group(function () {
    Route::get('/fixtures', 'index');
    Route::get('/fixtures/{fixture_id}', 'show');
    Route::post('/fixtures', 'store');
    Route::put('/fixtures/{fixture_id}', 'update');
    Route::delete('/fixtures/{fixture_id}', 'destroy');
});

Route::controller(SectionController::class)->group(function () {
    Route::get('/sections', 'index');
    Route::get('/sections/{section_id}', 'show');
    Route::post('/sections', 'store');
    Route::put('/sections/{section_id}', 'update');
    Route::delete('/sections/{section_id}', 'destroy');
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





// search custom function for client use 
// Route::get('/search', [SearchController::class, '__invoke'])->name('search');

// list all feature references
// unit - level - building - footprint - venue - address

