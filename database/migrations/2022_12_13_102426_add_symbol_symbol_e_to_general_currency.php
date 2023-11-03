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
        Schema::table('general_currencies', function (Blueprint $table) {
            $table->string ('symbol',15)->nullable ();
            $table->string ('symbol_e',15)->nullable ();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_currencies', function (Blueprint $table) {
            //
        });
    }
};
