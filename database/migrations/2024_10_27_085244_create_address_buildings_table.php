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
        Schema::create(TablesName::ADDRESS_BUILDINGS, function (Blueprint $table) {
            $table->id();

            $table->uuid('building_id');
            $table->uuid('address_id');
            $table->primary(['building_id', 'address_id']);

            $table->foreign('building_id')->references('building_id')->on(TablesName::BUILDINGS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::ADDRESS_BUILDINGS)->insert([
            'building_id' => '44444444-4444-4444-4444-444444444444',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
        DB::table(TablesName::ADDRESS_BUILDINGS)->insert([
            'building_id' => '44444444-4444-4444-4444-444444444443',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ADDRESS_BUILDINGS);
    }
};
