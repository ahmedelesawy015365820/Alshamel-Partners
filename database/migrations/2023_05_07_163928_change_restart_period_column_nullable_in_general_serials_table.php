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
        Schema::table('general_serials', function (Blueprint $table) {
            $table->string('restart_period')->nullable()->comment('Daily, monthly, yearly, open')->change();
            $table->unsignedBigInteger('restart_period_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_serials', function (Blueprint $table) {
            //
        });
    }
};
