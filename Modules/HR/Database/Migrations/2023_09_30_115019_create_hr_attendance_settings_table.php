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
        Schema::create('hr_attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('pre_in')->comment('مسموح الحضور قبل الموعد بكام دقيقة');
            $table->integer('post_in')->comment('مسموح البصمة بعد موعد الحضور بدون تاخير بكام دقيقة');
            $table->integer('absent_minutes')->comment('يعتبر متأخر ال كام دقيقة بعد الموعد (بعد فترة السماح السابقة) و يعتبر غائب لو حضر بعد ذلك');
            $table->integer('pre_out')->comment('مسموح الانصراف قبل الموعد بكام دقيقة');
            $table->integer('post_out')->comment('يعتير الانصراف بعد الموعد (بدون اضافي) بكام دقيقة');
            $table->integer('max_out')->comment('اقصى بصمة بعد موعد الانصراف بكام دقيقة');
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
        Schema::dropIfExists('hr_attendance_settings');
    }
};
