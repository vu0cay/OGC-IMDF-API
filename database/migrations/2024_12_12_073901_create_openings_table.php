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
        Schema::create(TablesName::OPENINGS, function (Blueprint $table) {
            $table->id();
            $table->uuid("opening_id")->unique();
            $table->unsignedBigInteger("feature_id");
            
            $table->unsignedInteger("opening_category_id");
            $table->geometry("geometry", srid: 4326);
            
            $table->geometry("display_point", "point", srid: 4326)->nullable();
            $table->uuid("level_id");
            $table->unsignedBigInteger('door_id')->nullable();

            $table->foreign("feature_id")->references("id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("door_id")->references("id")->on(TablesName::DOORS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("opening_category_id")->references("id")->on(TablesName::OPENING_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        DB::table(TablesName::OPENINGS)->insert([
            "opening_id" => "88888888-1111-2222-8888-888888888888",
            "feature_id" => 12,
            "geometry" => "LineString (100.0 0.0, 101.0 1.0)",
            "opening_category_id" => 4,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777",
            "door_id" => 1
        ]);         
        
        DB::table(TablesName::OPENINGS)->insert([
            "opening_id" => "88888888-3333-2222-8888-888888888888",
            "feature_id" => 12,
            "geometry" => "LineString (100.0 0.0, 101.0 1.0)",
            "opening_category_id" => 4,
            "display_point" => "POINT(100.0 1.0)",
            "level_id" => "77777777-7777-7777-7777-777777777777",
            "door_id" => 1
        ]);         
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OPENINGS);
    }
};
