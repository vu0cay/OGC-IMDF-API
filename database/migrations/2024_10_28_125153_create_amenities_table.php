<?php

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TablesName::AMENITIES, function (Blueprint $table) {
            $table->id();
            $table->uuid("amenity_id")->primary();
            $table->unsignedInteger("amenity_category_id");
            $table->unsignedInteger('feature_id');
            $table->geometry('geometry', srid: 4326)->nullable();
            $table->string("phone")->nullable();
            $table->string("website")->nullable();
            $table->string("hours")->nullable();
            $table->uuid("correlation_id")->nullable();
            $table->foreign('feature_id')->references('id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();            
            $table->foreign('amenity_category_id')->references('id')->on(TablesName::AMENITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });


        DB::table(TablesName::AMENITIES)->insert([
            "feature_id" => 2,
            "geometry" => "POINT (1.0 2.0)",
            "amenity_id" => "99999998-9999-9999-9999-999999999999",
            "amenity_category_id" => 100,
            "phone" => null,
            "website" => null,
            "hours" => null,
            "correlation_id" => null
        ]);

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::AMENITIES);
    }
};
