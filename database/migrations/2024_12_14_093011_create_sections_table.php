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
        Schema::create(TablesName::SECTIONS, function (Blueprint $table) {
            $table->id();
            $table->uuid("section_id")->unique();
            $table->unsignedBigInteger("feature_id");
            $table->unsignedInteger("section_category_id");
            $table->unsignedInteger("restriction_category_id")->nullable();
            $table->geometry("geometry", srid: 4326);
            $table->geometry("display_point", "point", srid: 4326)->nullable();
            
            $table->uuid("level_id");
            // $table->uuid("address_id")->nullable();
            $table->uuid("correlation_id")->nullable();

            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("section_category_id")->references("id")->on(TablesName::SECTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("restriction_category_id")->references("id")->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign("address_id")->references("address_id")->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table(TablesName::SECTIONS)->insert([
            "section_id" => "88888888-9876-8888-8888-888888888888",
            "feature_id" => 14,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "restriction_category_id" => 2,
            "section_category_id" => 1,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);
        //
        DB::table(TablesName::SECTIONS)->insert([
            "section_id" => "88888888-9876-9876-8888-888888888888",
            "feature_id" => 14,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "restriction_category_id" => 2,
            "section_category_id" => 1,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);
        //
        DB::table(TablesName::SECTIONS)->insert([
            "section_id" => "88888888-9876-9876-9876-888888888888",
            "feature_id" => 14,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "restriction_category_id" => 2,
            "section_category_id" => 1,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::SECTIONS);
    }
};
