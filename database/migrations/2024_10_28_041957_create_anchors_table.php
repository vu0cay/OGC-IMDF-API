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
        Schema::create(TablesName::ANCHORS, function (Blueprint $table) {
            $table->id();
            
            $table->uuid("anchor_id")->unique();
            $table->unsignedInteger('feature_id');
            $table->geometry('geometry', srid:4326);


            // $table->uuid("address_id")->nullable();
            $table->uuid("unit_id");
            $table->uuid("level_id");
            $table->primary(["anchor_id", "unit_id", "level_id"]);
            
            $table->foreign("feature_id")->references("id")->on('featuretests')->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->foreign("unit_id")->references("unit_id")->on(TablesName::UNITS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign("address_id")->references("address_id")->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        // DB::table(TablesName::FEATURES)->insert([
        //     "feature_id" => "99999999-9999-9999-9999-999999999999",
        //     "type" => "Feature",
        //     "feature_type" => "anchor",
        //     "geometry" => "Point(1.0 2.0)"
        // ]);


        DB::table(TablesName::ANCHORS)->insert([
            "anchor_id" => "99999999-9999-9999-9999-999999999999",
            "feature_id" => 3,
            "geometry" => "Point(1.0 2.0)",
            
            // "address_id" => "22222222-2222-2222-2222-222222222222",
            
            "unit_id" => "88888888-8888-8888-8888-888888888888",
            "level_id" => "77777777-7777-7777-7777-777777777777"
        ]);
    }

    
    /**
     * Reverse the migrations.
    */

    public function down(): void
    {
        Schema::dropIfExists(TablesName::ANCHORS);
    }
};
