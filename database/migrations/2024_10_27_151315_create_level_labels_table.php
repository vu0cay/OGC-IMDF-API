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
        Schema::create(TablesName::LEVEL_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('level_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['level_id', 'label_id']);
            $table->enum('type',allowed: ['name', 'short_name']);
            $table->foreign('level_id')->references('level_id')->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Ground Floor'
        ]);

        DB::table(TablesName::LEVEL_LABELS)->insert([
            'level_id' => "77777777-7777-7777-7777-777777777777",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);

        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => '1'
        ]);

        DB::table(TablesName::LEVEL_LABELS)->insert([
            'level_id' => "77777777-7777-7777-7777-777777777777",
            'label_id' => $newLabel->id,
            'type' => 'short_name'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::LEVEL_LABELS);
    }
};
