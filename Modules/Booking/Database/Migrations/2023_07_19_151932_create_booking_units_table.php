<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('name_e', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('description_e')->nullable();
            $table->double('price');
            $table->unsignedInteger('unit_status_id')->default(1);
            $table->unsignedInteger('company_id')->default(0);
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
        Schema::dropIfExists('booking_units');
    }
};
