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
            $table->enum('company_pays_rest', ['yes','no'])->default('yes');
            $table->enum('owner_pays_rest', ['yes','no'])->default('yes');

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
            $table->dropColumn('company_pays_rest');
            $table->dropColumn('owner_pays_rest');

        });
    }
};
