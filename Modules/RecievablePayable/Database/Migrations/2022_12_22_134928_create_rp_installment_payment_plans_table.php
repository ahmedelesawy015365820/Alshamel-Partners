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
        Schema::create('rp_installment_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string ('name')->nullable ();
            $table->string ('name_e')->nullable ();
            $table->unsignedTinyInteger ('is_default')->default (0);
            $table->unsignedTinyInteger ('is_active')->default (0)->comment ('1=Active, 0=Not Active');
            $table->text ('description')->nullable ();
            $table->text ('description_e')->nullable ();
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
        Schema::dropIfExists('rp_installment_payment_plans');
    }
};
