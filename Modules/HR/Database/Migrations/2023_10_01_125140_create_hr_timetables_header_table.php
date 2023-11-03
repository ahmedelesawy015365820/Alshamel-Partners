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
        Schema::create('hr_timetables_header', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('name_e',50);
            $table->unsignedInteger('timetable_types_id');
            $table->integer('tt_monthly_hours');
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
        Schema::dropIfExists('hr_timetables_header');
    }
};
