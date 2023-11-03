<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->string('name_e')->unique()->nullable();
            $table->string("phone_key", 10)->unique()->nullable();
            $table->integer("national_id_length")->nullable();
            $table->string("long_name", 100)->nullable();
            $table->string("long_name_e", 100)->nullable();
            $table->string("short_code", 10)->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->string("is_active")->default('active');
            $table->softDeletes();
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
        Schema::dropIfExists('general_countries');
    }
}
