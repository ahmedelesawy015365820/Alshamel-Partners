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
        Schema::create('general_document_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_status_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->date('date')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('prefix')->nullable();
            $table->unsignedBigInteger('serial_id')->nullable();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('related_document_id')->nullable();
            $table->unsignedBigInteger('related_document_number')->nullable();
            $table->unsignedBigInteger('related_document_prefix')->nullable();
            $table->unsignedBigInteger('sell_method_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('external_salesmen_id')->nullable();
            $table->double('total_invoice')->default(0);
            $table->double('invoice_discount')->default(0);
            $table->double('net_invoice')->default(0);
            $table->double('sell_method_discount')->default(0);
            $table->double('unrelaized_revenue')->default(0);
            $table->double('external_commission')->default(0);

            $table->double('revenue')->default(0);
            $table->double('unrealized_commission')->default(0);
            $table->double('commission')->default(0);
            $table->double('total_depit_note')->default(0);
            $table->string('remaining')->nullable();
            $table->string('complete_status')->default('UnDelivered')->comment('UnDelivered - partially - Delivered');
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
        Schema::dropIfExists('general_document_headers');
    }
};
