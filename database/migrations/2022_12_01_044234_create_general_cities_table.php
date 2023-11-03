<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('country_id')->nullable()->comment('PK- (from GEN_countries)');
            $table->unsignedInteger('governorate_id')->nullable()->comment('PK- (from GEN_Governorates)');
            $table->string('name', 100)->nullable()->comment('name arabic')->unique();
            $table->string('name_e', 100)->nullable()->comment('name en')->unique();
            $table->unsignedTinyInteger('is_active')->nullable()->default(0)->comment('1=Active, 0=Not Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_cities');
    }
};
