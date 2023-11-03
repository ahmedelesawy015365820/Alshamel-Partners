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
        Schema::create('general_voucher_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('branch_id');
            $table->date('date');
            $table->unsignedBigInteger('serial_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('salesmen_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->double('amount')->default(0);
            $table->string('serial_number')->nullable();
            $table->string('prefix')->nullable();
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
        Schema::dropIfExists('general_voucher_headers');
    }
};
