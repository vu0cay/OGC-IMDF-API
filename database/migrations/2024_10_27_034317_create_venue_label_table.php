<?php

use App\Constants\Features\TablesName;
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
        Schema::create(TablesName::VENUE_LABEL, function (Blueprint $table) {
            $table->id();
            $table->uuid('venue_id');
            $table->unsignedInteger('label_id');
            $table->primary(['venue_id', 'label_id']);

            $table->foreign('venue_id')->references('venue_id')->on(TablesName::VENUES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "en",
            "value" => "Test Venue"
        ]);
        DB::table(TablesName::VENUE_LABEL)->insert([
            "venue_id" => "11111111-1111-1111-1111-111111111111",
            "label_id" => 1
        ]);
     
        DB::table(TablesName::LABELS)->insert([
            "language_tag" => "vi",
            "value" => "Kiem tra Dia Diem"
        ]);
        DB::table(TablesName::VENUE_LABEL)->insert([
            "venue_id" => "11111111-1111-1111-1111-111111111111",
            "label_id" => 2
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_label');
    }
};
