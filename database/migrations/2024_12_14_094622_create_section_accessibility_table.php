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
        Schema::create(TablesName::SECTION_ACCESSIBILITY, function (Blueprint $table) {
            $table->id();
            $table->uuid('section_id');
            $table->unsignedInteger('accessibility_id');
            $table->primary(['section_id', 'accessibility_id']);

            $table->foreign('section_id')->references('section_id')->on(TablesName::SECTIONS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('accessibility_id')->references('id')->on(TablesName::ACCESSIBILITY_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            
            $table->timestamps();
        });
        
        DB::table(TablesName::SECTION_ACCESSIBILITY)->insert([
            ['section_id' => '88888888-9876-8888-8888-888888888888', 'accessibility_id' => 1],
            ['section_id' => '88888888-9876-8888-8888-888888888888', 'accessibility_id' => 2],
        ]);
        //
        DB::table(TablesName::SECTION_ACCESSIBILITY)->insert([
            ['section_id' => '88888888-9876-9876-8888-888888888888', 'accessibility_id' => 1],
            ['section_id' => '88888888-9876-9876-8888-888888888888', 'accessibility_id' => 2],
        ]);
        //
        DB::table(TablesName::SECTION_ACCESSIBILITY)->insert([
            ['section_id' => '88888888-9876-9876-9876-888888888888', 'accessibility_id' => 1],
            ['section_id' => '88888888-9876-9876-9876-888888888888', 'accessibility_id' => 2],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::SECTION_ACCESSIBILITY);
    }
};
