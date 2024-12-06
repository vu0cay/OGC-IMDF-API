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
        Schema::create(TablesName::UNIT_ACCESSIBILITY, function (Blueprint $table) {
            $table->id();
            $table->uuid('unit_id');
            $table->unsignedInteger('accessibility_id');
            $table->primary(['unit_id', 'accessibility_id']);

            $table->foreign('unit_id')->references('unit_id')->on(TablesName::UNITS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('accessibility_id')->references('id')->on(TablesName::ACCESSIBILITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        
        // DB::table(TablesName::UNIT_ACCESSIBILITY)->insert([
        //     ['unit_id' => '88888888-8888-8888-8888-888888888888', 'accessibility_id' => 1],
        //     ['unit_id' => '88888888-8888-8888-8888-888888888888', 'accessibility_id' => 2],
        // ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::UNIT_ACCESSIBILITY);
    }
};
