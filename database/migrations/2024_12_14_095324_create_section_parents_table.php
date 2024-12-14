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
        Schema::create(TablesName::SECTION_PARENTS, function (Blueprint $table) {
            $table->id();

            $table->uuid('section_id');
            $table->uuid('parent_section_id');
            $table->primary(['section_id', 'parent_section_id']);

            $table->foreign('section_id')->references('section_id')->on(TablesName::SECTIONS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('parent_section_id')->references('section_id')->on(TablesName::SECTIONS)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::SECTION_PARENTS)->insert([
            'section_id' => '88888888-9876-8888-8888-888888888888',
            'parent_section_id' => '88888888-9876-9876-8888-888888888888'
        ]);

        DB::table(TablesName::SECTION_PARENTS)->insert([
            'section_id' => '88888888-9876-8888-8888-888888888888',
            'parent_section_id' => '88888888-9876-9876-9876-888888888888'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::SECTION_PARENTS);
    }
};
