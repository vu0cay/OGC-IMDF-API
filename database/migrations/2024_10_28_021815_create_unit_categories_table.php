<?php

use App\Constants\Features\TablesName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TablesName::UNIT_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $categories = [
            'auditorium',
            'brick',
            'classroom',
            'column',
            'concrete',
            'conferenceroom',
            'drywall',
            'elevator',
            'escalator',
            'fieldofplay',
            'firstaid',
            'fitnessroom',
            'foodservice',
            'footbridge',
            'glass',
            'huddleroom',
            'kitchen',
            'laboratory',
            'library',
            'lobby',
            'lounge',
            'mailroom',
            'mothersroom',
            'movietheater',
            'movingwalkway',
            'nonpublic',
            'office',
            'opentobelow',
            'parking',
            'phoneroom',
            'platform',
            'privatelounge',
            'ramp',
            'recreation',
            'restroom',
            'restroom.family',
            'restroom.female',
            'restroom.female.wheelchair',
            'restroom.male',
            'restroom.male.wheelchair',
            'restroom.transgender',
            'restroom.transgender.wheelchair',
            'restroom.unisex',
            'restroom.unisex.wheelchair',
            'restroom.wheelchair',
            'road',
            'room',
            'serverroom',
            'shower',
            'smokingarea',
            'stairs',
            'steps',
            'storage',
            'structure',
            'terrace',
            'theater',
            'unenclosedarea',
            'unspecified',
            'vegetation',
            'waitingroom',
            'walkway',
            'walkway.island',
            'wood',
        ];  

        DB::table(TablesName::UNIT_CATEGORIES)
            ->insert(array_map(fn($name) => ['name' => $name], $categories));

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_categories');
    }
};
