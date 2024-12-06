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
        Schema::create('address_levels', function (Blueprint $table) {
            $table->id();

            $table->uuid('level_id');
            $table->uuid('address_id');
            $table->primary(['level_id', 'address_id']);

            $table->foreign('level_id')->references('level_id')->on(TablesName::LEVELS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        // DB::table('address_levels')->insert([
        //     'level_id' => '77777777-7777-7777-7777-777777777777',
        //     'address_id' => '22222222-2222-2222-2222-222222222222'
        // ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_levels');
    }
};
