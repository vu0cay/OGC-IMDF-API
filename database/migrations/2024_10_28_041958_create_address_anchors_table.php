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
        Schema::create('address_anchors', function (Blueprint $table) {
            $table->id();

            $table->uuid('anchor_id');
            $table->uuid('address_id');
            $table->primary(['anchor_id', 'address_id']);

            $table->foreign('anchor_id')->references('anchor_id')->on(TablesName::ANCHORS)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('address_id')->references('address_id')->on(TablesName::ADDRESSES)->cascadeOnUpdate()->cascadeOnDelete();
        });


        DB::table('address_anchors')->insert([
            'anchor_id' => '99999999-9999-9999-9999-999999999999',
            'address_id' => '22222222-2222-2222-2222-222222222222'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_anchors');
    }
};
