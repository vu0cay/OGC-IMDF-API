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
        Schema::create(TablesName::ADDRESS_VENUES, function (Blueprint $table) {
            $table->id();

            $table->uuid('venue_id');
            $table->uuid('address_id');
            $table->primary(['venue_id', 'address_id']);

            $table->foreign('venue_id')->references('venue_id')->on(TablesName::VENUES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::ADDRESS_VENUES)->insert([
            'venue_id' => '11111111-1111-1111-1111-111111111111',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ADDRESS_VENUES);
    }
};
