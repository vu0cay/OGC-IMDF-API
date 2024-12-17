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
        Schema::create(TablesName::AMENTITY_LABEL, function (Blueprint $table) {
            $table->id();

            $table->uuid('amenity_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['amenity_id', 'label_id']);
            $table->enum('type',allowed: ['name', 'alt_name']);
            $table->foreign('amenity_id')->references('amenity_id')->on(TablesName::AMENITIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });


        $newLabel = Label::create([
            'language_tag' => 'vi',
            'value' => 'stairs'
        ]);

        DB::table(TablesName::AMENTITY_LABEL)->insert([
            'amenity_id' => "99999998-9999-9999-9999-999999999999",
            'label_id' => $newLabel->id,
            'type' => 'alt_name'
        ]);

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::AMENTITY_LABEL);
    }
};
