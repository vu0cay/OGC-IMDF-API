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
        Schema::create(TablesName::KIOSK_LABELS, function (Blueprint $table) {
            $table->id();

            $table->uuid('kiosk_id');
            $table->unsignedBigInteger('label_id');
            $table->primary(['kiosk_id', 'label_id']);
            $table->enum('type',['name', 'alt_name']);
            $table->foreign('kiosk_id')->references('kiosk_id')->on(TablesName::KIOSKS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::KIOSK_LABELS);
    }
};
