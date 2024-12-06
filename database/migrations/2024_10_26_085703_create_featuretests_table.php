<?php

use App\Constants\Features\FeatureType;
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
        Schema::create('featuretests', function (Blueprint $table) {
            $table->id();
            $table->string('feature_type');
            $table->timestamps();
        });
        DB::table('featuretests')->insert(array_map(function ($name) {
            return ['feature_type' => $name];
        }, FeatureType::getConstanst()));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featuretests');
    }
};
