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
        Schema::create(TablesName::FOOTPRINT_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('footprint_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['footprint_id', 'label_id']);
            $table->enum('type',['name']);
            $table->foreign('footprint_id')->references('footprint_id')->on(TablesName::FOOTPRINTS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
        });


        // $newLabel = Label::create([
        //     'language_tag' => 'en',
        //     'value' => 'ground of CICT building',
        //     // 'short_name' => '1'
        // ]);

        // DB::table(TablesName::FOOTPRINT_LABELS)->insert([
        //     'footprint_id' => "55555555-5555-5555-5555-555555555555",
        //     'label_id' => $newLabel->id,
        //     'type' => 'name'
        // ]);

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FOOTPRINT_LABELS);
    }
};
