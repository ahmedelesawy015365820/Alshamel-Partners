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
            $table->enum('rent_type', ['Accrued', 'Actual'])->default('Accrued')->comment('ايجار محصل و ايجار فعلي');
            $table->enum('plus_extra_revenues', ['yes', 'no'])->default('no')->comment('اضافة ايرادات اضافية');
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
            $table->dropColumn('rent_type');
            $table->dropColumn('plus_extra_revenues');
        });
    }
};
