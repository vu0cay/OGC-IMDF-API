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
        Schema::create(TablesName::SECTION_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('section_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['section_id', 'label_id']);
            $table->enum('type', ['name', 'alt_name']);
            $table->foreign('section_id')->references('section_id')->on(TablesName::SECTIONS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });


        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Section'
        ]);

        DB::table(TablesName::SECTION_LABELS)->insert([
            'section_id' => "88888888-9876-8888-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
        //
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Section 2'
        ]);

        DB::table(TablesName::SECTION_LABELS)->insert([
            'section_id' => "88888888-9876-9876-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
        //
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Section 3'
        ]);

        DB::table(TablesName::SECTION_LABELS)->insert([
            'section_id' => "88888888-9876-9876-9876-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::SECTION_LABELS);
    }
};
