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
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('order_type')->nullable();
            $table->longText('sales_note')->nullable();
            $table->double('sub_total', 20,2)->nullable();
            $table->double('total_tax', 20,2)->default(0);
            $table->double('due_amount', 20,2)->default(0);
            $table->double('total', 20,2)->nullable();
            $table->string('type')->nullable();
            $table->double('profit', 20,2)->default(0);
            $table->string('status')->nullable();
            $table->double('all_discount', 20,2)->default(0);
            $table->integer('customer_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('transfer_branch_id')->nullable();
            $table->integer('table_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('returned_invoice')->nullable();
            $table->string('return_type')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('serial_id')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('tax_type')->nullable();
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
        Schema::dropIfExists('pos_orders');
    }
};
