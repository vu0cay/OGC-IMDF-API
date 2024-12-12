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
        Schema::create(TablesName::OPENING_ACCESSIBILITY, function (Blueprint $table) {
            $table->id();
            $table->uuid('opening_id');
            $table->unsignedInteger('accessibility_id');
            $table->primary(['opening_id', 'accessibility_id']);

            $table->foreign('opening_id')->references('opening_id')->on(TablesName::OPENINGS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('accessibility_id')->references('id')->on(TablesName::ACCESSIBILITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        
        DB::table(TablesName::OPENING_ACCESSIBILITY)->insert([
            ['opening_id' => '88888888-1111-2222-8888-888888888888', 'accessibility_id' => 1],
            ['opening_id' => '88888888-1111-2222-8888-888888888888', 'accessibility_id' => 2],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::OPENING_ACCESSIBILITY);
    }
};
