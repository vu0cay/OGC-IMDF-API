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
        Schema::create(TablesName::KIOSKS, function (Blueprint $table) {
            $table->id();
            $table->uuid("kiosk_id")->unique();
            $table->unsignedBigInteger("feature_id");
            $table->geometry("geometry", srid: 4326);
            $table->geometry("display_point", "point", srid: 4326)->nullable();
            $table->uuid("level_id");
            $table->uuid("anchor_id")->nullable();

            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("anchor_id")->references("anchor_id")->on(TablesName::ANCHORS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        DB::table(TablesName::ANCHORS)->insert([
            "anchor_id" => "99999999-8888-9999-9999-999999999999",
            "feature_id" => 3,
            "geometry" => "POINT(1.0 2.0)",
            "unit_id" => "88888888-8888-8888-8888-888888888888",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);

        DB::table(TablesName::KIOSKS)->insert([
            "kiosk_id" => "88888888-7777-8888-8888-888888888888",
            "feature_id" => 9,
            "geometry" => "Polygon ((100.0 0.0, 101.0 0.0, 101.0 1.0, 100.0 1.0, 100.0 0.0 ))",
            "display_point" => "POINT(1.0 2.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777",
            "anchor_id" => "99999999-8888-9999-9999-999999999999"
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::KIOSKS);
    }
};
