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
        Schema::create(TablesName::FEATURES, function (Blueprint $table) {
            $table->id();
            $table->uuid("feature_id")->primary();
            $table->string('type');
            $table->string('feature_type');
            $table->geometry('geometry', srid: 4326)->nullable();
            $table->timestamps();
        });
       
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::FEATURES);
    }
};
