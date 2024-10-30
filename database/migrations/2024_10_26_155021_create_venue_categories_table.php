<?php

use App\Constants\Features\Category\VenueCategory;
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
        Schema::create(TablesName::VENUE_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table(TablesName::VENUE_CATEGORIES)->insert(array_map(fn($name) => ['name' => $name], array_values(VenueCategory::getConstanst())));

    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::VENUE_CATEGORIES);
    }
};
