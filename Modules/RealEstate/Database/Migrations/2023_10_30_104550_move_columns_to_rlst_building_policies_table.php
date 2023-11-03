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
        Schema::table('rlst_policies', function (Blueprint $table) {
            $table->dropColumn('after_expenses');
            $table->dropColumn('plus_extra_revenues');
            $table->dropColumn('collected_rent_type');
        });

        Schema::table('rlst_building_policies', function (Blueprint $table) {
            $table->enum('after_expenses', ['yes','no'])->default('yes');
            $table->enum('plus_extra_revenues', ['yes','no'])->default('yes');
            $table->enum('collected_rent_type', ['actual', 'accrued'])->default('actual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_policies', function (Blueprint $table) {
            $table->enum('after_expenses', ['yes','no'])->default('yes');
            $table->enum('plus_extra_revenues', ['yes','no'])->default('yes');
            $table->enum('collected_rent_type', ['actual', 'accrued'])->default('actual');
        });

        Schema::table('rlst_building_policies', function (Blueprint $table) {
            $table->dropColumn('after_expenses');
            $table->dropColumn('plus_extra_revenues');
            $table->dropColumn('collected_rent_type');
        });
    }
};
