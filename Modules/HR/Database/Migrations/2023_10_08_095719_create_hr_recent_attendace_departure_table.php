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
        Schema::create('hr_recent_attendance_departure', function (Blueprint $table) {
     
            $table->increments('id');            
            $table->unsignedTinyInteger('month')->min(1)->max(12)->comment('report month');
            $table->year('year')->comment('report year');
            $table->unsignedInteger('employee_id');
            
            $table->string('name')->comment('employee name');
            $table->date('day')->comment('yyyy-mm-dd in report month and year');
            $table->time('attend_1')->nullable()->comment('1st shift attend time');
            $table->time('depart_1')->nullable()->comment('1st shift depart time');
            $table->time('attend_2')->nullable()->comment('2nd shift attend time');
            $table->time('depart_2')->nullable()->comment('2nd shift depart time');

            $table->unsignedSmallInteger('late')->nullable()->comment('total late time in mins');
            $table->unsignedSmallInteger('overtime')->nullable()->comment('total over time in mins');
            $table->unsignedSmallInteger('exact_hours')->nullable()->comment('total exact working time in mins');
            $table->enum('absence', ['attended', 'absent 1', 'absent 2', 'absent'])->nullable()->comment('day status');
            $table->string('notes')->nullable();

            $table->timestamps();

            $table->unique(['year', 'month', 'employee_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_recent_attendance_departure');
    }
};
