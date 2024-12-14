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
        Schema::create(TablesName::GEOFENCE_PARENTS, function (Blueprint $table) {
            $table->id();

            $table->uuid('geofence_id');
            $table->uuid('parent_geofence_id');
            $table->primary(['geofence_id', 'parent_geofence_id']);

            $table->foreign('geofence_id')->references('geofence_id')->on(TablesName::GEOFENCES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('parent_geofence_id')->references('geofence_id')->on(TablesName::GEOFENCES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::GEOFENCE_PARENTS)->insert([
            'geofence_id' => '12345678-9876-8888-8888-888888888888',
            'parent_geofence_id' => '87654321-9876-8888-8888-888888888888'
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::GEOFENCE_PARENTS);
    }
};
