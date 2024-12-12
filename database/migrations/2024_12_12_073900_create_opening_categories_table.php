<?php

use App\Constants\Features\Category\OpeningCategory;
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
        Schema::create(TablesName::OPENING_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        DB::table(TablesName::OPENING_CATEGORIES)
        ->insert(array_map(fn($name) => ['name' => $name], array_values(OpeningCategory::getConstanst())));

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OPENING_CATEGORIES);
    }
};
