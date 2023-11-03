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
        Schema::table('boards_rent_sell_methods', function (Blueprint $table) {
            $table->renameColumn('target_ratio', 'target_calculation_ratio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards_rent_sell_methods', function (Blueprint $table) {
            $table->renameColumn('target_ratio', 'target_calculation_ratio');
        });
    }
};
