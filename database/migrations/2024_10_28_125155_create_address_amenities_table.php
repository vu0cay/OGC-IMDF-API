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
        Schema::create(TablesName::ADDRESS_AMENITIES, function (Blueprint $table) {
            $table->id();

            $table->uuid('amenity_id');
            $table->uuid('address_id');
            $table->primary(['amenity_id', 'address_id']);

            $table->foreign('amenity_id')->references('amenity_id')->on(TablesName::AMENITIES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table(TablesName::ADDRESS_AMENITIES)->insert([
            'amenity_id' => '99999998-9999-9999-9999-999999999999',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ADDRESS_AMENITIES);
    }
};
