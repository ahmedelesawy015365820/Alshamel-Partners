<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string("name")->comment("Name Arabic");
            $table->string("name_e")->comment("Name English");
            $table->string("symbol",10);
            $table->unsignedBigInteger('stock_market_id');
            $table->unsignedBigInteger('sector_id');
            $table->timestamps();

//            $table->foreign('stock_market_id')->references('id')->on('stock_markets');
//            $table->foreign('sector_id')->references('id')->on('stock_sectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
