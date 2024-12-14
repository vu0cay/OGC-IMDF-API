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
        Schema::create(TablesName::GEOFENCES, function (Blueprint $table) {
            $table->id();
            $table->uuid("geofence_id")->unique();
            $table->unsignedBigInteger("feature_id");
            $table->unsignedInteger("geofence_category_id");
            $table->unsignedInteger("restriction_category_id")->nullable();
            $table->geometry("geometry", srid: 4326);
            $table->geometry("display_point", "point", srid: 4326)->nullable();

            $table->uuid("correlation_id")->nullable();

            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("geofence_category_id")->references("id")->on(TablesName::GEOFENCE_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("restriction_category_id")->references("id")->on(TablesName::RESTRICTION_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table(TablesName::GEOFENCES)->insert([
            "geofence_id" => "12345678-9876-8888-8888-888888888888",
            "feature_id" => 8,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "restriction_category_id" => 1,
            "geofence_category_id" => 1,
            "display_point" => "POINT(100.0 1.0)"
        ]);
        // parent
        DB::table(TablesName::GEOFENCES)->insert([
            "geofence_id" => "87654321-9876-8888-8888-888888888888",
            "feature_id" => 8,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "restriction_category_id" => 1,
            "geofence_category_id" => 1,
            "display_point" => "POINT(100.0 1.0)"
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::GEOFENCES);
    }
};
