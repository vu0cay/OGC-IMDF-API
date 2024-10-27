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
        Schema::create('level_building', function (Blueprint $table) {
            $table->id();
            $table->uuid('level_id');
            $table->uuid('building_id');
            $table->primary(['level_id', 'building_id']);

            $table->foreign('level_id')->references('level_id')->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('building_id')->references('building_id')->on(TablesName::BUILDINGS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        DB::table(TablesName::LEVEL_BUILDING)->insert([
            ['level_id' => '77777777-7777-7777-7777-777777777777', 'building_id' => '44444444-4444-4444-4444-444444444444'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_building');
    }
};
