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
        Schema::create(TablesName::RELATIONSHIPS, function (Blueprint $table) {
            $table->id();
            $table->uuid('relationship_id')->primary();
            $table->unsignedInteger('relationship_category_id');
            $table->unsignedInteger('feature_id');
            // $table->unsignedInteger("origin_id")->nullable();
            // $table->unsignedInteger("intermediary_id")->nullable();
            // $table->unsignedInteger("destination_id")->nullable();

            $table->enum('direction', ['directed','undirected'])->nullable();
            $table->string('hours')->nullable();

            $table->foreign('feature_id')->references('id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('relationship_category_id')->references('id')->on(TablesName::RELATIONSHIP_CATEGORIES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign('origin_id')->references('id')->on(TablesName::FEATURE_REFERENCES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign('intermediary_id')->references('id')->on(TablesName::FEATURE_REFERENCES)->cascadeOnUpdate()->cascadeOnDelete();
            // $table->foreign('destination_id')->references('id')->on(TablesName::FEATURE_REFERENCES)->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        DB::table(TablesName::RELATIONSHIPS)->insert([
            'relationship_id' => "88888888-8888-1234-8888-888888888888",
            'relationship_category_id' => 6,
            'direction' => 'directed',
            'feature_id' => 13,
            // 'origin_id' => 1,
            // 'intermediary_id' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::RELATIONSHIPS);
    }
};
