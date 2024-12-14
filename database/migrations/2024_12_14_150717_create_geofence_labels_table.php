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
        Schema::create(TablesName::GEOFENCE_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('geofence_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['geofence_id', 'label_id']);
            $table->enum('type', ['name', 'alt_name']);
            $table->foreign('geofence_id')->references('geofence_id')->on(TablesName::GEOFENCES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });


        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Geofence'
        ]);

        DB::table(TablesName::GEOFENCE_LABELS)->insert([
            'geofence_id' => "12345678-9876-8888-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
        // parent
        $newLabel = Label::create([
            'language_tag' => 'en',
            'value' => 'Test Geofence 2'
        ]);

        DB::table(TablesName::GEOFENCE_LABELS)->insert([
            'geofence_id' => "87654321-9876-8888-8888-888888888888",
            'label_id' => $newLabel->id,
            'type' => 'name'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::GEOFENCE_LABELS);
    }
};
