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
        Schema::table('booking_units', function (Blueprint $table) {
            $table->unsignedInteger('unit_status_id')->default(12)->change();
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
            $table->unsignedInteger('unit_status_id')->default(1)->change();
        });
    }
};
