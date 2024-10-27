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
        Schema::create('level_label', function (Blueprint $table) {
            $table->id();
            $table->uuid('level_id');
            $table->unsignedInteger('label_id');

            $table->primary(['level_id', 'label_id']);

            $table->foreign('level_id')->references('level_id')->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "en",
            "value" => "Ground Floor"
        ]);
        DB::table(TablesName::LEVEL_LABEL)->insert([
            "level_id" => "77777777-7777-7777-7777-777777777777",
            "label_id" => 5
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_label');
    }
};
