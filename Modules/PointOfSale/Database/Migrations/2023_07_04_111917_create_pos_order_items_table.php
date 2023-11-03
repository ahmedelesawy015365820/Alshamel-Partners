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
        Schema::create('pos_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->integer('variant_id')->nullable();
            $table->string('type')->nullable()->nullable();
            $table->float('quantity')->nullable();
            $table->double('price', 20,2)->nullable();
            $table->double('discount', 20,2)->default(0);
            $table->double('sub_total', 20,2)->default(0)->nullable();
            $table->integer('tax_id')->nullable()->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('adjust_stock_type_id')->default(0);
            $table->string('note')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('pos_order_items');
    }
};
