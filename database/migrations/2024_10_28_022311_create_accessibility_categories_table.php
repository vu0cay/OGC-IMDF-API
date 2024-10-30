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
        Schema::create(TablesName::ACCESSIBILITY_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $categories = [
            "assisted.listening" ,
            "braille",
            "hearing",
            "hearingloop",
            "signlanginterpreter",
            "tactilepaving",
            "tdd",
            "trs",
            "volume",
            "wheelchair",
        ];  

        DB::table(TablesName::ACCESSIBILITY_CATEGORIES)
            ->insert(array_map(fn($name) => ['name' => $name], $categories));

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ACCESSIBILITY_CATEGORIES);
    }
};
