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
        Schema::create(TablesName::ADDRESSES, function (Blueprint $table) {
            $table->id();
            $table->uuid("address_id")->primary();
            $table->string("address");
            $table->string("unit")->nullable();
            $table->string("locality");
            $table->string("province")->nullable();
            $table->string("country");
            $table->string("postal_code")->nullable();
            $table->string("postal_code_ext")->nullable();
            $table->string("postal_code_vanity")->nullable();
            $table->timestamps();
        });

        # unqualified building address
        DB::table(TablesName::FEATURES)->insert([
            "feature_id" => "22222222-2222-2222-2222-222222222222",
            "type" => "Feature",
            "feature_type" => "address",
            "geometry" => null
        ]);
        DB::table(TablesName::ADDRESSES)->insert([
            "address_id" => "22222222-2222-2222-2222-222222222222",
            "address" => "123 E. Main Street",
            "unit" => null,
            "locality" => "Anytown",
            "province" => "US-CA",
            "country" => "US",
            "postal_code" => "12345",
            "postal_code_ext" => "1111"
        ]);


        # unit-qualified address
        DB::table(TablesName::FEATURES)->insert([
            "feature_id" => "33333333-3333-3333-3333-333333333333",
            "type" => "Feature",
            "feature_type" => "address",
            "geometry" => null
        ]);
        DB::table(TablesName::ADDRESSES)->insert([
            "address_id" => "33333333-3333-3333-3333-333333333333",
            "address" => "123 E. Main Street",
            "unit" => "1A",
            "locality" => "Anytown",
            "province" => "US-CA",
            "country" => "US",
            "postal_code" => "12345",
            "postal_code_ext" => "1111"
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TablesName::ADDRESSES);
    }
};
