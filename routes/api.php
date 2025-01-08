<?php

use App\Constants\Features\TablesName;
use App\Contracts\ExportDatasets;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\Features\AddressController;
use App\Http\Controllers\Features\AmenityController;
use App\Http\Controllers\Features\AnchorController;
use App\Http\Controllers\Features\BuildingController;
use App\Http\Controllers\Features\FixtureController;
use App\Http\Controllers\Features\FootprintController;
use App\Http\Controllers\Features\GeofenceController;
use App\Http\Controllers\Features\KioskController;
use App\Http\Controllers\Features\LevelController;
use App\Http\Controllers\Features\ManifestController;
use App\Http\Controllers\Features\OccupantController;
use App\Http\Controllers\Features\OpeningController;
use App\Http\Controllers\Features\RelationshipController;
use App\Http\Controllers\Features\UnitController;
use App\Http\Controllers\Features\VenueController;
use App\Http\Controllers\Functions\SearchController;

use App\Http\Controllers\SectionController;
use App\Http\Controllers\SensorController;
use App\Models\Features\Footprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


$version = 'v'.$version = DB::table('manifests')->pluck('version')->last();


Route::controller(ManifestController::class)->group(function () use($version) {
    Route::get($version.'/manifests', 'index');
});

Route::controller(AddressController::class)->group(function () use($version) {

    Route::get($version.'/addresses', 'index');
    Route::get($version.'/addresses/{address_id}', 'show');
    Route::post($version.'/addresses', 'store');
    Route::put($version.'/addresses/{address_id}', 'update');
    Route::delete($version.'/addresses/{address_id}', 'destroy');

});

Route::controller(VenueController::class)->group(function () use($version) {

    Route::get($version.'/venues', 'index');
    Route::get($version.'/venues/{venue_id}', 'show');
    Route::post($version.'/venues', 'store');
    Route::put($version.'/venues/{venue_id}', 'update');
    Route::delete($version.'/venues/{venue_id}', 'destroy');

});

Route::controller(FootprintController::class)->group(function () use($version) {

    Route::get($version.'/footprints', 'index');
    Route::get($version.'/footprints/{footprint_id}', 'show');
    Route::post($version.'/footprints', 'store');
    Route::put($version.'/footprints/{footprint_id}', 'update');
    Route::delete($version.'/footprints/{footprint_id}', 'destroy');

});

Route::controller(BuildingController::class)->group(function () use($version) {

    Route::get($version.'/buildings', 'index');
    Route::get($version.'/buildings/{building_id}', 'show');
    Route::post($version.'/buildings', 'store');
    Route::put($version.'/buildings/{building_id}', 'update');
    Route::delete($version.'/buildings/{building_id}', 'destroy');

});

Route::controller(LevelController::class)->group(function () use($version) {

    Route::get($version.'/levels', 'index');
    Route::get($version.'/levels/{level_id}', 'show');
    Route::post($version.'/levels', 'store');
    Route::put($version.'/levels/{level_id}', 'update');
    Route::delete($version.'/levels/{level_id}', 'destroy');

});

Route::controller(UnitController::class)->group(function () use($version) {

    Route::get($version.'/units', 'index');
    Route::get($version.'/units/{unit_id}', 'show');
    Route::post($version.'/units', 'store');
    Route::put($version.'/units/{unit_id}', 'update');
    Route::delete($version.'/units/{unit_id}', 'destroy');

});

Route::controller(AnchorController::class)->group(function () use($version) {
    Route::get($version.'/anchors', 'index');
    Route::get($version.'/anchors/{anchor_id}', 'show');
    Route::post($version.'/anchors', 'store');
    Route::put($version.'/anchors/{anchor_id}', 'update');
    Route::delete($version.'/anchors/{anchor_id}', 'destroy');
});

Route::controller(AmenityController::class)->group(function () use($version) {
    Route::get($version.'/amenities', 'index');
    Route::get($version.'/amenities/{amenity_id}', 'show');
    Route::post($version.'/amenities', 'store');
    Route::put($version.'/amenities/{amenity_id}', 'update');
    Route::delete($version.'/amenities/{amenity_id}', 'destroy');
});

Route::controller(KioskController::class)->group(function () use($version) {
    Route::get($version.'/kiosks', 'index');
    Route::get($version.'/kiosks/{kiosk_id}', 'show');
    Route::post($version.'/kiosks', 'store');
    Route::put($version.'/kiosks/{kiosk_id}', 'update');
    Route::delete($version.'/kiosks/{kiosk_id}', 'destroy');
});

Route::controller(OpeningController::class)->group(function () use($version){
    Route::get($version.'/openings', 'index');
    Route::get($version.'/openings/{opening_id}', 'show');
    Route::post($version.'/openings', 'store');
    Route::put($version.'/openings/{opening_id}', 'update');
    Route::delete($version.'/openings/{opening_id}', 'destroy');
});

Route::controller(OccupantController::class)->group(function () use($version){
    Route::get($version.'/occupants', 'index');
    Route::get($version.'/occupants/{occupant_id}', 'show');
    Route::post($version.'/occupants', 'store');
    Route::put($version.'/occupants/{occupant_id}', 'update');
    Route::delete($version.'/occupants/{occupant_id}', 'destroy');
});

Route::controller(RelationshipController::class)->group(function () use($version){
    Route::get($version.'/relationships', 'index');
    Route::get($version.'/relationships/{relationship_id}', 'show');
    Route::post($version.'/relationships', 'store');
    Route::put($version.'/relationships/{relationship_id}', 'update');
    Route::delete($version.'/relationships/{relationship_id}', 'destroy');
});

Route::controller(DetailController::class)->group(function () use($version){
    Route::get($version.'/details', 'index');
    Route::get($version.'/details/{detail_id}', 'show');
    Route::post($version.'/details', 'store');
    Route::put($version.'/details/{detail_id}', 'update');
    Route::delete($version.'/details/{detail_id}', 'destroy');
});

Route::controller(FixtureController::class)->group(function () use($version){
    Route::get($version.'/fixtures', 'index');
    Route::get($version.'/fixtures/{fixture_id}', 'show');
    Route::post($version.'/fixtures', 'store');
    Route::put($version.'/fixtures/{fixture_id}', 'update');
    Route::delete($version.'/fixtures/{fixture_id}', 'destroy');
});

Route::controller(SectionController::class)->group(function () use($version) {
    Route::get($version.'/sections', 'index');
    Route::get($version.'/sections/{section_id}', 'show');
    Route::post($version.'/sections', 'store');
    Route::put($version.'/sections/{section_id}', 'update');
    Route::delete($version.'/sections/{section_id}', 'destroy');
});

Route::controller(GeofenceController::class)->group(function () use($version) {
    Route::get($version.'/geofences', 'index');
    Route::get($version.'/geofences/{geofence_id}', 'show');
    Route::post($version.'/geofences', 'store');
    Route::put($version.'/geofences/{geofence_id}', 'update');
    Route::delete($version.'/geofences/{geofence_id}', 'destroy');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





// search custom function for client use 
Route::get('/search', [SearchController::class, '__invoke'])->name('search');

// list all feature references
// unit - level - building - footprint - venue - address


// export datasets
Route::get('/export-spatial-data', function () {
    return ExportDatasets::exportRoutesToJsonZip();
});
