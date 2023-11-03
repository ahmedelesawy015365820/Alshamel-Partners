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
        Schema::table('booking_units', function (Blueprint $table) {
            $table->bigInteger('number_of_individuals')->default(0)->comment('عدد الأفراد');
            $table->double('extra_guest_price')->default(0)->comment('سعر الفرد الإضافي');
            $table->unsignedInteger('booking_floor_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_units', function (Blueprint $table) {

        });
    }
};
