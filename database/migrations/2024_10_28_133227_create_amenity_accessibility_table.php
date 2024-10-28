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
        Schema::create(TablesName::AMENITY_ACCESSIBILITY, function (Blueprint $table) {
            $table->id();
            $table->uuid('amenity_id');
            $table->unsignedInteger('accessibility_id');
            $table->primary(['amenity_id', 'accessibility_id']);

            $table->foreign('amenity_id')->references('amenity_id')->on(TablesName::AMENITIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('accessibility_id')->references('id')->on(TablesName::ACCESSIBILITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        DB::table(TablesName::AMENITY_ACCESSIBILITY)->insert([
            ['amenity_id' => '99999998-9999-9999-9999-999999999999', 'accessibility_id' => 7],
            ['amenity_id' => '99999998-9999-9999-9999-999999999999', 'accessibility_id' => 8],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_accessibility');
    }
};
