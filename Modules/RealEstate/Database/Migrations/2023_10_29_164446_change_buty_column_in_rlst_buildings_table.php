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
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            $table->unsignedTinyInteger('building_type_id');
            $table->dropColumn('bu_ty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_building_wallet', function (Blueprint $table) {
            $table->unsignedTinyInteger('bu_ty', 1);
            $table->dropColumn('building_type_id');
        });
    }
};
