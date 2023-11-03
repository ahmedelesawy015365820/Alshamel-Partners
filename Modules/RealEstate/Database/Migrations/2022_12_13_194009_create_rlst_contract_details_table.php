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
        Schema::create('rlst_contract_details', function (Blueprint $table) {
            $table->id();
            // $table->foreignId("reservation_id")->nullable();
            $table->foreignId('building_id');
            $table->foreignId('unit_id');
            $table->foreignId('installment_payment_plans_id');
            $table->unsignedBigInteger('contract_id');
            $table->double('reservation_value');
            $table->double('unit_value');
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
        Schema::dropIfExists('rlst_contract_details');
    }
};
