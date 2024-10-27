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
        Schema::create(TablesName::VENUE_CATEGORIES, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "airport"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "airport.intl"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "businesscampus"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "casino"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "communitycenter"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "conventioncenter"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "governmentfacility"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "healthcarefacility"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "hotel"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "museum"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "parkingfacility"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "resort"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "retailstore"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "shoppingcenter"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "stadium"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "stripmall"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "theater"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "themepark"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "trainstation"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "transitstation"
        ]);
        DB::table(TablesName::VENUE_CATEGORIES)->insert([
            "name" => "university"
        ]);
         
        /*
        airport
        airport.intl
        aquarium
        businesscampus
        casino
        communitycenter
        conventioncenter
        governmentfacility
        healthcarefacility
        hotel
        museum
        parkingfacility
        resort
        retailstore
        shoppingcenter
        stadium
        stripmall
        theater
        themepark
        trainstation
        transitstation
        university
        */
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_categories');
    }
};
