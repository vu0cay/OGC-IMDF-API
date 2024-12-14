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
        Schema::create(TablesName::ADDRESS_SECTIONS, function (Blueprint $table) {
            $table->id();

            $table->uuid('section_id');
            $table->uuid('address_id');
            $table->primary(['section_id', 'address_id']);

            $table->foreign('section_id')->references('section_id')->on(TablesName::SECTIONS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::ADDRESS_SECTIONS)->insert([
            'section_id' => '88888888-9876-8888-8888-888888888888',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ADDRESS_SECTIONS);
    }
};
