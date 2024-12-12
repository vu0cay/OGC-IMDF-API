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
        Schema::create(TablesName::OCCUPANT_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('occupant_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['occupant_id', 'label_id']);
            $table->enum('type',['name']);
            $table->foreign('occupant_id')->references('occupant_id')->on(TablesName::OCCUPANTS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
        });

        // venue label
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Occupant'
        ]);

        DB::table(TablesName::OCCUPANT_LABELS)->insert([
            'occupant_id' => "11111111-3333-4444-1111-111111111111",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OCCUPANT_LABELS);
    }
};
