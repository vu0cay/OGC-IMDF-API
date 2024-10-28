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
        Schema::create('anchors', function (Blueprint $table) {
            $table->id();
            $table->uuid("anchor_id")->unique();
            $table->uuid("address_id")->nullable();
            $table->uuid("unit_id");
            $table->uuid("level_id");
            $table->primary(["anchor_id", "unit_id", "level_id"]);
            
            $table->foreign("anchor_id")->references("feature_id")->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("unit_id")->references("unit_id")->on(TablesName::UNITS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("level_id")->references("level_id")->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("address_id")->references("address_id")->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anchors');
    }
};
