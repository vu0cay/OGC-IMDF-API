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
        Schema::create(TablesName::BUILDINGS, function (Blueprint $table) {
            $table->id();
            $table->uuid("building_id")->primary();
            $table->unsignedBigInteger("feature_id");
            $table->unsignedInteger('building_category_id');
            $table->unsignedInteger('restriction_category_id')->nullable();
            $table->geometry("display_point", "point", 4326)->nullable();
            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('building_category_id')->references('id')->on(TablesName::BUILDING_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('restriction_category_id')->references('id')->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->timestamps();
        });

        DB::table(TablesName::BUILDINGS)->insert([
            "building_id" => "44444444-4444-4444-4444-444444444444",
            "feature_id" => 4,
            "building_category_id" => 1,
            "restriction_category_id" => 1,
            "display_point" => "POINT(1.0 2.0)",
        ]);

        DB::table(TablesName::BUILDINGS)->insert([
            "building_id" => "44444444-4444-4444-4444-444444444443",
            "feature_id" => 4,
            "building_category_id" => 1,
            "restriction_category_id" => 1,
            "display_point" => "POINT(1.0 2.0)",
        ]);
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::BUILDINGS);
    }
};
