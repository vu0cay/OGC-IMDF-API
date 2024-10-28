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
            $table->unsignedInteger("unit_category_id");
            $table->unsignedInteger("restriction_category_id")->nullable();
            $table->geometry("display_point", "point", 4326)->nullable();
            $table->uuid("level_id");
            
            $table->primary(["unit_id", "level_id"]);

            $table->foreign("unit_id")->references("feature_id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("unit_category_id")->references("id")->on(TablesName::UNIT_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("restriction_category_id")->references("id")->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
        
        
        DB::table(TablesName::FEATURES)->insert([
            "feature_id" => "88888888-8888-8888-8888-888888888888",
            "type" => "Feature",
            "feature_type" => "unit",
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))"
        ]);
        DB::table(TablesName::UNITS)->insert([
            "unit_id" => "88888888-8888-8888-8888-888888888888",
            "unit_category_id" => 47,
            // "restriction_category_id" => null,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);

        // unit labels
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "en",
            "value" => "Ball room"
        ]);
        DB::table(TablesName::FEATURE_LABEL)->insert([
            "feature_id" => "88888888-8888-8888-8888-888888888888",
            "label_id" => 6
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
