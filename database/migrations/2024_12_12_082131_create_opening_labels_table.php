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
        Schema::create(TablesName::OPENING_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('opening_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['opening_id', 'label_id']);
            $table->enum('type',['name', 'alt_name']);
            $table->foreign('opening_id')->references('opening_id')->on(TablesName::OPENINGS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });

        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Door A',
        ]);

        DB::table(TablesName::OPENING_LABELS)->insert([
            'opening_id' => "88888888-1111-2222-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OPENING_LABELS);
    }
};
