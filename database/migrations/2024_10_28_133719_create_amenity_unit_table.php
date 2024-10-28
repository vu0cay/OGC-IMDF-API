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
        Schema::create(TablesName::AMENITY_UNIT, function (Blueprint $table) {
            $table->id();
            $table->uuid('amenity_id');
            $table->uuid('unit_id');
            $table->primary(['amenity_id', 'unit_id']);

            $table->foreign('amenity_id')->references('amenity_id')->on(TablesName::AMENITIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('unit_id')->references('unit_id')->on(TablesName::UNITS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        DB::table(TablesName::AMENITY_UNIT)->insert([
            "amenity_id" => "99999998-9999-9999-9999-999999999999",
            "unit_id" => "88888888-8888-8888-8888-888888888888",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::AMENITY_UNIT);
    }
};
