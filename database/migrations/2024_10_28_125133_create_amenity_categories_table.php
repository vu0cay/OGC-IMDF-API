<?php

use App\Constants\Features\Category\AmenityCategory;
use App\Constants\Features\TablesName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TablesName::AMENITY_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table(TablesName::AMENITY_CATEGORIES)->insert(array_map(function ($name) {
            return ['name' => $name];
        }, AmenityCategory::getConstanst()));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::AMENITY_CATEGORIES);
    }
};
