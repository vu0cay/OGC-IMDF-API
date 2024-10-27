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
        Schema::create(TablesName::FOOTPRINT_BUILDING, function (Blueprint $table) {
            $table->id();
            $table->uuid('footprint_id');
            $table->uuid('building_id');
            $table->primary(['footprint_id', 'building_id']);

            $table->foreign('footprint_id')->references('footprint_id')->on(TablesName::FOOTPRINTS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('building_id')->references('building_id')->on(TablesName::BUILDINGS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        DB::table(TablesName::FOOTPRINT_BUILDING)->insert([
            ['footprint_id' => '66666666-6666-6666-6666-666666666666', 'building_id' => '44444444-4444-4444-4444-444444444444'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footprint_building');
    }
};
