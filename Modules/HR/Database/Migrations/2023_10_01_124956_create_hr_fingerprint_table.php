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
        Schema::create('hr_fingerprint', function (Blueprint $table) {
            $table->id();
            $table->integer('att_code');
            $table->dateTime('vdate');
            $table->boolean('att_type');
            $table->integer('machine_id');
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
        Schema::dropIfExists('hr_fingerprint');
    }
};
