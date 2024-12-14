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
        Schema::create(TablesName::FIXTURES, function (Blueprint $table) {
            $table->id();
            $table->uuid("fixture_id")->unique();
            $table->unsignedBigInteger("feature_id");
            $table->unsignedInteger("fixture_category_id");
            $table->geometry("geometry", srid: 4326);
            $table->geometry("display_point", "point", srid: 4326)->nullable();
            $table->uuid("anchor_id")->nullable();
            $table->uuid("level_id");
            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("fixture_category_id")->references("id")->on(TablesName::FIXTURE_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("anchor_id")->references("anchor_id")->on(TablesName::ANCHORS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table(TablesName::FIXTURES)->insert([
            "fixture_id" => "12345678-8888-8888-8888-888888888888",
            "feature_id" => 6,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "fixture_category_id" => 5,
            "display_point" => "POINT(100.0 1.0)",
            "anchor_id" => "99999999-9999-9999-9999-999999999999",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FIXTURES);
    }
};
