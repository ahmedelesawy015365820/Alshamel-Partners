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
        Schema::create('boards_rent_panels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_e')->nullable();
            $table->text('description')->nullable();
            $table->text('description_e')->nullable();
            $table->json('price');
            $table->string('code');
            $table->string('new_code')->nullable();
            $table->string('size');
            $table->text('note')->nullable();
            $table->enum('face', ['A', 'B', 'Multi','One-Face'])->default('A');
            $table->unsignedBigInteger('unit_status_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            // locations
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('governorate_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('avenue_id')->nullable();
            $table->unsignedBigInteger('street_id')->nullable();
            $table->double('lat');
            $table->double('lng');
            $table->timestamps();
            // drop lat
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards_rent_panels');
    }
};
