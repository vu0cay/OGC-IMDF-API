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
        Schema::create(TablesName::FOOTPRINT_LABEL, function (Blueprint $table) {
            $table->id();
            $table->uuid('footprint_id');
            $table->unsignedInteger('label_id');

            $table->primary(['footprint_id', 'label_id']);

            $table->foreign('footprint_id')->references('footprint_id')->on(TablesName::FOOTPRINTS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "en",
            "value" => "East Wing"
        ]);
        DB::table(TablesName::FOOTPRINT_LABEL)->insert([
            "footprint_id" => "66666666-6666-6666-6666-666666666666",
            "label_id" => 4
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footprint_label');
    }
};
