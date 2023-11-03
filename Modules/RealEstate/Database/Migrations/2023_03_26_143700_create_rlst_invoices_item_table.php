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
        Schema::create('rlst_invoices_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("invoice_id")->nullable();
            $table->unsignedBigInteger("item_id")->nullable();
            $table->integer('quantity');
            $table->double('amount');
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
        Schema::dropIfExists('rlst_invoices_item');
    }
};
