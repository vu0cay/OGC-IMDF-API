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
        Schema::create(TablesName::VALIDITIES, function (Blueprint $table) {
            $table->id();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->dateTime('modified');
            $table->timestamps();
        });

        DB::table(TablesName::VALIDITIES)->insert([
            "start" => "2018-03-01T00:00:00+05:00",
            "end" => "2018-10-01T11:59:59+05:00",
            "modified" => "2018-01-01T12:34:56+05:00",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::VALIDITIES);
    }
};
