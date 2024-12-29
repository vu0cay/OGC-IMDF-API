<?php

use App\Models\Features\Manifest;
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
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->string('version');
            $table->string('generated_by');
            $table->string('language');
            
            $table->timestamps();
        });
        $manifest = Manifest::create([
            'version' => '1.0.0',
            'generated_by' => 'FME 2019.0 b19238',
            'language' => 'en-US'
        ]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manifests');
    }
};
