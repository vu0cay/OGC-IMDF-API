<?php

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
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
        Schema::create(TablesName::UNIT_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('unit_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['unit_id', 'label_id']);
            $table->enum('type',allowed: ['name', 'alt_name']);
            $table->foreign('unit_id')->references('unit_id')->on(TablesName::UNITS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
        });

        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Ball room',
        ]);

        DB::table(TablesName::UNIT_LABELS)->insert([
            'unit_id' => "88888888-8888-8888-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::UNIT_LABELS);
    }
};
