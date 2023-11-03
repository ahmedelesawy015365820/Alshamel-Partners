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
        Schema::table('boards_rent_panels', function (Blueprint $table) {
            $table->double('price_year')->nullable();
            $table->double('price_6month')->nullable();
            $table->double('price_3month')->nullable();
            $table->double('price_month')->nullable();
            $table->double('price_2week')->nullable();
            $table->tinyInteger('is_double_face')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_multi')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
