<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockSalePurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_sale_purchase_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('stock_id');
            $table->date('date');
            $table->time('time');
            $table->enum('type', ['Buy', 'Sell', 'Gift']);
            $table->longText('note');
            $table->unsignedInteger('qty');
            $table->double('price', 15, 8);
            $table->double('fixed_commission', 15, 8);
            $table->double('other_commission', 15, 8);
            $table->double('net_value', 15, 8);
            $table->double('trade_average', 15, 8);
            $table->double('old_qty', 15, 8);
            $table->unsignedInteger('old_price');
            $table->unsignedInteger('old_sell_qty');
            $table->double('old_sell_price', 15, 8);
            $table->double('old_average', 15, 8);
            $table->unsignedInteger('new_qty');
            $table->double('new_average', 15, 8);
            $table->double('profit', 15, 8);
            $table->timestamps();/**/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_sale_purchase_details');
    }
}
