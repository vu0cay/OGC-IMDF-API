<?php

use App\Constants\Features\Category\RestrictionCategory;
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
        Schema::create(TablesName::RESTRICTION_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
      
        DB::table(TablesName::RESTRICTION_CATEGORIES)
            ->insert(
                array_map(fn($name) =>
                    ['name' => $name], array_values(RestrictionCategory::getConstanst()))
                );




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::RESTRICTION_CATEGORIES);
    }
};
