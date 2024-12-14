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
        Schema::create(TablesName::DETAILS, function (Blueprint $table) {
            $table->id();
            $table->uuid('detail_id')->primary();
            $table->unsignedBigInteger("feature_id");
            $table->geometry("geometry", srid: 4326);
            $table->uuid('level_id');
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });


        DB::table(TablesName::DETAILS)->insert([
            "detail_id" => "88888888-1234-5678-9999-888888888888",
            "feature_id" => 5,
            "geometry" => "MultiLineString((100.0 0.0, 101.0 1.0, 102.0 2.0),(102.0 2.0, 103.0 3.0, 104.0 4.0))",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::DETAILS);
    }
};
