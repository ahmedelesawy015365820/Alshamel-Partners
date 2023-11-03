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
            $table->dropUnique(['building_id', 'date']);
            $table->unique(['building_id', 'year', 'month'], 'building_id_year_month_unique');
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
            $table->dropUnique('building_id_year_month_unique');
            $table->unique(['building_id', 'date'], 'building_id_date_unique');
        });
    }
};
