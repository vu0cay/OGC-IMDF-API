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
        Schema::create(TablesName::GEOFENCE_LEVEL, function (Blueprint $table) {
            $table->id();
            $table->uuid('geofence_id');
            $table->uuid('level_id');
            $table->primary(['geofence_id', 'level_id']);
            $table->foreign('geofence_id')->references('geofence_id')->on(TablesName::GEOFENCES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('level_id')->references('level_id')->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
        DB::table(TablesName::GEOFENCE_LEVEL)->insert([
            ['geofence_id' => '12345678-9876-8888-8888-888888888888', 'level_id' => '77777777-7777-7777-7777-777777777777'],
        ]);
        // parent
        DB::table(TablesName::GEOFENCE_LEVEL)->insert([
            ['geofence_id' => '87654321-9876-8888-8888-888888888888', 'level_id' => '77777777-7777-7777-7777-777777777777'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::GEOFENCE_LEVEL);
    }
};
