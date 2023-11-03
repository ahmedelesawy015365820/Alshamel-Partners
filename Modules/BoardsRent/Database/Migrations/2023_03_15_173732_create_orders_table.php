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
        Schema::create('boards_rent_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->foreignId('serial_id')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('salesman_id');
            $table->unsignedBigInteger('sell_method_id');
            $table->unsignedBigInteger('document_id');
            $table->date('date');
            $table->text('note')->nullable();
            $table->double('discount')->nullable();
            $table->double('paid')->nullable();
            $table->double('due')->nullable();
            $table->double('total')->nullable();
            $table->boolean('is_stripe')->default(0);
            $table->string("serial_number")->nullable();
            $table->string("prefix")->nullable();
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
        Schema::dropIfExists('boards_rent_orders');
    }
};
