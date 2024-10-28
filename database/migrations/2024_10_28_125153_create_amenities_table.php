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
        Schema::create(TablesName::AMENITIES, function (Blueprint $table) {
            $table->id();
            $table->uuid("amenity_id")->primary();
            $table->unsignedInteger("amenity_category_id");

            $table->string("phone")->nullable();
            $table->string("website")->nullable();
            $table->string("hours")->nullable();
            $table->uuid("address_id")->nullable();
            $table->uuid("correlation_id")->nullable();

            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('amenity_id')->references('feature_id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('amenity_category_id')->references('id')->on(TablesName::AMENITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        DB::table(TablesName::FEATURES)->insert([
            "feature_id" => "99999998-9999-9999-9999-999999999999",
            "type" => "Feature",
            "feature_type" => "amenity",
            "geometry" => "Point(1.0 2.0)"
        ]);
        DB::table(TablesName::AMENITIES)->insert([
            "amenity_id" => "99999998-9999-9999-9999-999999999999",
            "address_id" => "22222222-2222-2222-2222-222222222222",
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
