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
        Schema::create('boards_rent_sell_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_e');
            $table->double("commission_ratio")->default(0);
            $table->double("target_ratio")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards_rent_sell_methods');
    }
};
