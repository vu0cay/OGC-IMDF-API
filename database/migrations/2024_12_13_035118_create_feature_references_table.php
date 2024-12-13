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
        Schema::create(TablesName::FEATURE_REFERENCES, function (Blueprint $table) {
            $table->id();
            
            $table->uuid('feature_id')->unique();
            $table->unsignedBigInteger('feature_type_id');

            $table->foreign('feature_type_id')->references('id')->on(TablesName::FEATURES)->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
        

        DB::table(TablesName::FEATURE_REFERENCES)->insert([
            'feature_id' => "88888888-8888-8888-8888-888888888888",
            'feature_type_id' => 15
        ]);

        DB::table(TablesName::FEATURE_REFERENCES)->insert([
            'feature_id' => "88888888-1111-2222-8888-888888888888",
            'feature_type_id' => 12
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FEATURE_REFERENCES);
    }
};
