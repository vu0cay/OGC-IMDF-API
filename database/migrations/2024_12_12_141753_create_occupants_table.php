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
        Schema::create(TablesName::OCCUPANTS, function (Blueprint $table) {
            $table->id();
            $table->uuid("occupant_id")->primary();
            
            $table->unsignedInteger('occupant_category_id');
            $table->unsignedInteger('feature_id');
            $table->uuid("anchor_id");
            $table->uuid("correlation_id")->nullable();

            $table->string('hours')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('validity_id')->nullable();

            $table->foreign('feature_id')->references('id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('occupant_category_id')->references('id')->on(TablesName::OCCUPANT_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('anchor_id')->references('anchor_id')->on(TablesName::ANCHORS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('validity_id')->references('id')->on(TablesName::VALIDITIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });


        DB::table(TablesName::OCCUPANTS)->insert([
            "occupant_id" => "11111111-3333-4444-1111-111111111111",
            "feature_id" => 11,
            "validity_id" => 1,
            "occupant_category_id" => DB::table(TablesName::OCCUPANT_CATEGORIES)->where("name", "restaurant")->first()->id,
            "hours" => "Mo-Fr 08:30-20:00",
            "website" => "http://example.com",
            "phone" => "+12225551212",
            "anchor_id" => "99999999-9999-9999-9999-999999999999",       
            "correlation_id" => null     
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OCCUPANTS);
    }
};
