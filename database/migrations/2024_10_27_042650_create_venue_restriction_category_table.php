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
        Schema::create(TablesName::VENUE_RESTRICTION_CATEGORY, function (Blueprint $table) {
            $table->id();
            $table->uuid('venue_id');
            $table->unsignedInteger('restriction_category_id');
            $table->primary(['venue_id', 'restriction_category_id']);

            $table->foreign('venue_id')->references('venue_id')->on(TablesName::VENUES)->onDelete('cascade');
            $table->foreign('restriction_category_id')->references('id')->on(TablesName::RESTRICTION_CATEGORIES)->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::VENUE_RESTRICTION_CATEGORY);
    }
};
