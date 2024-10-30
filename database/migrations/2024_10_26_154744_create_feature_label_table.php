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
        Schema::create(TablesName::FEATURE_LABEL, function (Blueprint $table) {
            $table->id();
            $table->uuid('feature_id');
            $table->unsignedInteger('label_id');

            $table->primary(['feature_id', 'label_id']);

            $table->foreign('feature_id')->references('feature_id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('label_id')->references('id')->on(TablesName::LABELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });

        
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FEATURE_LABEL);
    }
};
