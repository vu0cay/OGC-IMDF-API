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
        Schema::create(TablesName::BUILDING_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('building_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['building_id', 'label_id']);
            $table->enum('type',['name', 'alt_name']);
            $table->foreign('building_id')->references('building_id')->on(TablesName::BUILDINGS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        // building labels
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Parking Garage 1'
        ]);

        DB::table(TablesName::BUILDING_LABELS)->insert([
            'building_id' => "44444444-4444-4444-4444-444444444444",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::BUILDING_LABELS);
    }
};
