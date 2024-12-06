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
        Schema::create('venue_labels', function (Blueprint $table) {
            $table->id();

            $table->uuid('venue_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['venue_id', 'label_id']);

            $table->foreign('venue_id')->references('venue_id')->on(TablesName::VENUES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
        });

        // venue label
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Venue'
        ]);

        DB::table('venue_labels')->insert([
            'venue_id' => "11111111-1111-1111-1111-111111111111",
            'label_id' => $newLabel->id
        ]);

        $newLabel = Label::create([
            'language_tag' => 'vi',
            'value' => 'Kiem tra dia diem'
        ]);

        DB::table('venue_labels')->insert([
            'venue_id' => "11111111-1111-1111-1111-111111111111",
            'label_id' => $newLabel->id
        ]);


        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_labels');
    }
};
