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
        Schema::create(TablesName::FOOTPRINTS, function (Blueprint $table) {
            $table->id();

            $table->uuid('footprint_id')->primary();
            
            $table->unsignedBigInteger('feature_id');
            
            $table->unsignedInteger('footprint_category_id');
            
            $table->geometry('geometry', srid:4326);

            $table->foreign('feature_id')->references('id')->on('featuretests')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('footprint_category_id')->references('id')->on(TablesName::FOOTPRINT_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();


            // $table->timestamps();
        });

        #Ground level footprint for single Building
        // DB::table(TablesName::FEATURES)->insert([
        //     "feature_id" => "55555555-5555-5555-5555-555555555555",
        //     "type" => "Feature",
        //     "feature_type" => "footprint",
        //     "geometry" => "Polygon(( 100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))"
        // ]);

        DB::table(TablesName::FOOTPRINTS)->insert([
            "footprint_id" => "55555555-5555-5555-5555-555555555555",
            "feature_id" => 7,
            "footprint_category_id" => 2,
            "geometry" => "Polygon(( 100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
        ]);

        #Composite aerial footprint
        // DB::table(TablesName::FEATURES)->insert([
        //     "feature_id" => "66666666-6666-6666-6666-666666666666",
        //     "type" => "Feature",
        //     "feature_type" => "footprint",
        //     "geometry" => "MULTIPOLYGON( ((180.0 40.0, 180.0 50.0, 170.0 50.0, 170.0 40.0, 180.0 40.0)),
        //                                  ((-170.0 40.0, -170.0 50.0, -180.0 50.0, -180.0 40.0, -170.0 40.0))
        //                                 )"
        // ]);

        DB::table(TablesName::FOOTPRINTS)->insert([
            "footprint_id" => "66666666-6666-6666-6666-666666666666",
            "feature_id" => 7,
            "footprint_category_id" => 1,
            "geometry" => "MULTIPOLYGON( ((180.0 40.0, 180.0 50.0, 170.0 50.0, 170.0 40.0, 180.0 40.0)),
                                         ((-170.0 40.0, -170.0 50.0, -180.0 50.0, -180.0 40.0, -170.0 40.0))
                                        )",
        ]);

        // footprint label
        // DB::table(TablesName::LABELS)->insert([
        //     "language_tag" => "en",
        //     "value" => "East Wing"
        // ]);

        // DB::table(TablesName::FEATURE_LABEL)->insert([
        //     "feature_id" => "66666666-6666-6666-6666-666666666666",
        //     "label_id" => 4
        // ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FOOTPRINTS);
    }
};
