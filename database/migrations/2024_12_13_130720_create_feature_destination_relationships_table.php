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
        Schema::create(TablesName::FEATURE_DESTINATION_RELATIONSHIPS, function (Blueprint $table) {

            $table->unsignedBigInteger('feature_reference_id');
            $table->uuid('relationship_id');
            $table->primary(['feature_reference_id', 'relationship_id']);

            $table->foreign('feature_reference_id')->references('id')->on(TablesName::FEATURE_REFERENCES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('relationship_id')->references('relationship_id')->on(TablesName::RELATIONSHIPS)->cascadeOnUpdate()->cascadeOnDelete();
        });

        // null
        // DB::table(TablesName::FEATURE_DESTINATION_RELATIONSHIPS)->insert([
        //     'feature_reference_id' => 1,
        //     'relationship_id' => '88888888-8888-1234-8888-888888888888'
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FEATURE_DESTINATION_RELATIONSHIPS);
    }
};
