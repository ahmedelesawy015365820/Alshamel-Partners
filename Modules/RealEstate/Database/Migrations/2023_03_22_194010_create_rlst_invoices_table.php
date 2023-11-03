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
        Schema::create('rlst_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->foreignId('salesman_id')->nullable();
            $table->date("date")->nullable();
            $table->foreignId('serial_id')->nullable();
            $table->foreignId('document_id')->nullable();
            $table->foreignId('building_id')->nullable();
            $table->foreignId('unit_id')->nullable();
            // $table->foreignId('plan_installments_id');
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
            $table->double ('flat_amount')->nullable();
            $table->double ('paid_amount')->nullable();
            $table->integer('payment_plan_id')->nullable();
            $table->string('module_type')->nullable();
            $table->string("prefix")->nullable();
            $table->integer("serial_number")->nullable();
            $table->foreignId('payment_method_id')->nullable();
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
        Schema::dropIfExists('rlst_invoices');
    }
};
