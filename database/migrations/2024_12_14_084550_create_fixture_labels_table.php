<?php

use App\Constants\Features\TablesName;
use App\Models\FeaturesCategory\Label;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TablesName::FIXTURE_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('fixture_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['fixture_id', 'label_id']);
            $table->enum('type', ['name', 'alt_name']);
            $table->foreign('fixture_id')->references('fixture_id')->on(TablesName::FIXTURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });


        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Fixture'
        ]);

        DB::table(TablesName::FIXTURE_LABELS)->insert([
            'fixture_id' => "12345678-8888-8888-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'alt_name'
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FIXTURE_LABELS);
    }
};
