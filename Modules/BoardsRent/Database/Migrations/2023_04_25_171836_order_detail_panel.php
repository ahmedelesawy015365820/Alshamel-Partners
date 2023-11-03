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
        Schema::create('boards_rent_order_detail_panel', function (Blueprint $table) {
            $table->unsignedBigInteger('order_detail_id');
            $table->foreign('order_detail_id')->references('id')->on('boards_rent_order_details')->onDelete('cascade');
            $table->unsignedBigInteger('panel_id');
            $table->foreign('panel_id')->references('id')->on('boards_rent_panels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards_rent_order_detail_panel');

    }
};
