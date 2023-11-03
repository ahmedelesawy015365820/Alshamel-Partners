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
        Schema::create('rlst_installments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('pay_type')->default("down_payment");
            $table->double("amount");
            $table->unsignedBigInteger("currency_id");
            $table->double("rest_amount");
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
        Schema::dropIfExists('rlst_installments');
    }
};
