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
        Schema::table('rlst_building_policies', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->tinyInteger('year');
            $table->tinyInteger('month');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_building_policies', function (Blueprint $table) {
            $table->date('date');
            $table->dropColumn('year');
            $table->dropColumn('month');
        });
    }
};
