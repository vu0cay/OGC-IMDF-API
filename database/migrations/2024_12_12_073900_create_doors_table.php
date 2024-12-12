<?php

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
        Schema::create(TablesName::DOORS, function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('automatic');
            $table->string('material');
            $table->timestamps();
        });

        DB::table(TablesName::DOORS)->insert([
            "automatic" => true,
            "material" => "glass",
            "type" => "sliding",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::DOORS);
    }
};
