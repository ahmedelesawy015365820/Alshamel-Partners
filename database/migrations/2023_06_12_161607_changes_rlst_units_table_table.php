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
        Schema::table('rlst_units', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->change();
            $table->string('name', 100)->nullable()->change();
            $table->string('name_e', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_units', function (Blueprint $table) {
            $table->string('code', 20)->change();
            $table->string('name', 100)->change();
            $table->string('name_e', 100)->change();
        });
    }
};
