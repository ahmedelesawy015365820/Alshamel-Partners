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
        Schema::create('rp_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->date ('date')->nullable();
            $table->double ('rate')->nullable()->comment ('سعر العملة');
            $table->unsignedInteger ('currency_id');
            $table->unsignedInteger ('customer_id');
            $table->double ('debit')->nullable()->comment ('دَين');
            $table->double ('credit')->nullable()->comment ('مدين');
            $table->double ('local_debit')->nullable()->comment ('سعر العملة * مدين');
            $table->double ('local_credit')->nullable()->comment ('سعر العملة * مدين');
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
        Schema::dropIfExists('rp_opening_balances');
    }
};
