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
        Schema::create('hr_timetables_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('timetables_header_id');
            $table->integer('day_no');
            $table->time('shift1_from');
            $table->time('shift1_to');
            $table->time('shift2_from')->nullable();
            $table->time('shift2_to')->nullable();
            $table->boolean('is_off');


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
        Schema::dropIfExists('hr_timetables_detail');
    }
};
