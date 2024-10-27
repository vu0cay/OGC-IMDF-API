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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid("building_id")->primary();
            $table->unsignedInteger('building_category_id');
            $table->unsignedInteger('restriction_category_id')->nullable();

            $table->geometry("display_point", "point", 4326)->nullable();
            $table->uuid("address_id");

            $table->foreign("address_id")->references("address_id")->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('building_category_id')->references('id')->on(TablesName::BUILDING_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('restriction_category_id')->references('id')->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });


        DB::table("features")->insert([
            "feature_id" => "44444444-4444-4444-4444-444444444444",
            "type" => "Feature",
            "feature_type" => "building",
            "geometry" => null
        ]);
        DB::table("buildings")->insert([
            "building_id" => "44444444-4444-4444-4444-444444444444",
            "building_category_id" => 1,
            "restriction_category_id" => 1,
            "display_point" => "POINT(1.0 2.0)",
            "address_id" => "22222222-2222-2222-2222-222222222222"
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
