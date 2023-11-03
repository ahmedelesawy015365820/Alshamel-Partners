<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpenningBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openning_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('wallet_id');
            $table->date('date');
            $table->unsignedInteger("qty");
            $table->double('price', 15, 8);
            $table->double('net', 15, 8);
            $table->integer("currency_id");
            $table->timestamps();

            $table->foreign('stock_id')->references('id')->on('stocks');
//            $table->foreign('wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('openning_balances');
    }
}
