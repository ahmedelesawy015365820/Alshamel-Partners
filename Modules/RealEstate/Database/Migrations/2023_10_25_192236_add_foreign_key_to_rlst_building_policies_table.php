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
            //$table->foreign('building_id')->references('id')->on('rlst_buildings');
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
            //$table->dropForeign('rlst_building_policies_building_id_foreign');
        });
    }
};
