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
        Schema::create(TablesName::LEVELS, function (Blueprint $table) {
            $table->id();
            $table->uuid("level_id")->primary();
            $table->unsignedBigInteger('feature_id');
            $table->unsignedInteger('level_category_id');
            $table->unsignedInteger('restriction_category_id')->nullable();
            $table->geometry('geometry', srid:4326);
            $table->boolean("outdoor");
            $table->integer("ordinal");
            $table->geometry("display_point", srid: 4326)->nullable();
            // $table->uuid("address_id")->nullable();
            $table->foreign('feature_id')->references('id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('level_category_id')->references('id')->on(TablesName::LEVEL_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('restriction_category_id')->references('id')->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign("address_id")->references("address_id")->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });


        DB::table(TablesName::LEVELS)->insert([
            "level_id" => "77777777-7777-7777-7777-777777777777",
            "feature_id" => 10,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "level_category_id" => 7,
            // "restriction_category_id" => null,
            "ordinal" => 0,
            "outdoor" => false,
            "display_point" => "POINT(100.0 1.0)",
            // "address_id" => null
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::LEVELS);
    }
};
