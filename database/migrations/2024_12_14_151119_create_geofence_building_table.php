<?php

use App\Constants\Features\TablesName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TablesName::GEOFENCE_BUILDING, function (Blueprint $table) {
            $table->id();
            $table->uuid('geofence_id');
            $table->uuid('building_id');
            $table->primary(['geofence_id', 'building_id']);
            $table->foreign('geofence_id')->references('geofence_id')->on(TablesName::GEOFENCES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('building_id')->references('building_id')->on(TablesName::BUILDINGS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
        DB::table(TablesName::GEOFENCE_BUILDING)->insert([
            ['geofence_id' => '12345678-9876-8888-8888-888888888888', 'building_id' => '44444444-4444-4444-4444-444444444444'],
        ]);
        // parent
        DB::table(TablesName::GEOFENCE_BUILDING)->insert([
            ['geofence_id' => '87654321-9876-8888-8888-888888888888', 'building_id' => '44444444-4444-4444-4444-444444444444'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::GEOFENCE_BUILDING);
    }
};
