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
        Schema::create(TablesName::UNITS, function (Blueprint $table) {
            $table->id();
            $table->uuid("unit_id")->unique();
            $table->unsignedBigInteger("feature_id");
            $table->unsignedInteger("unit_category_id");
            $table->unsignedInteger("restriction_category_id")->nullable();
            $table->geometry("geometry", "Polygon", srid: 4326);
            $table->geometry("display_point", "point", srid: 4326)->nullable();
            $table->uuid("level_id");
            $table->primary(["unit_id", "level_id"]);
            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("unit_category_id")->references("id")->on(TablesName::UNIT_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("restriction_category_id")->references("id")->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->timestamps();
        });

        DB::table(TablesName::UNITS)->insert([
            "unit_id" => "88888888-8888-8888-8888-888888888888",
            "feature_id" => 15,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "unit_category_id" => 47,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::UNITS);
    }
};
