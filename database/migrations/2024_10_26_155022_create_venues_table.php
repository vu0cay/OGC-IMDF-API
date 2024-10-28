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
        Schema::create(TablesName::VENUES, function (Blueprint $table) {
            $table->id();
            $table->uuid("venue_id")->primary();
            $table->unsignedInteger('venue_category_id');
            $table->unsignedInteger('restriction_category_id')->nullable();
            $table->uuid('address_id');
            
            $table->string('hours');
            $table->string('phone');
            $table->string('website');
            $table->geometry('display_point', srid:4326);

            $table->foreign('venue_id')->references('feature_id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('venue_category_id')->references('id')->on(TablesName::VENUE_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('restriction_category_id')->references('id')->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });


        DB::table(TablesName::FEATURES)->insert([
            "feature_id" => "11111111-1111-1111-1111-111111111111",
            "type" => "Feature",
            "feature_type" => "venue",
            "geometry" => "POLYGON ((100.0  0.0, 101.0  0.0, 101.0  1.0, 100.0  1.0, 100.0  0.0))"
        ]);
        DB::table(TablesName::VENUES)->insert([
            "venue_id" => "11111111-1111-1111-1111-111111111111",
            "venue_category_id" => DB::table(TablesName::VENUE_CATEGORIES)->where("name", "shoppingcenter")->first()->id,
            "restriction_category_id" => null,
            "hours" => "Mo-Fr 08:30-20:00",
            "website" => "http://example.com",
            "phone" => "+12225551212",
            "display_point" => "POINT(100.0 1.0)",
            "address_id" => "22222222-2222-2222-2222-222222222222"
        ]);

        // venue label
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "en",
            "value" => "Test Venue"
        ]);
        DB::table(TablesName::FEATURE_LABEL)->insert([
            "feature_id" => "11111111-1111-1111-1111-111111111111",
            "label_id" => 1
        ]);
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "vi",
            "value" => "Kiem tra Dia Diem"
        ]);
        DB::table(TablesName::FEATURE_LABEL)->insert([
            "feature_id" => "11111111-1111-1111-1111-111111111111",
            "label_id" => 2
        ]);
        // 

   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
