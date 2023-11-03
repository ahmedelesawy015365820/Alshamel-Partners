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
        Schema::create('rp_break_downs', function (Blueprint $table) {
            $table->id();
            $table->double ('rate')->comment ('سعر العملة');
            $table->unsignedInteger ('currency_id')->nullable();
            $table->unsignedInteger ('customer_id')->nullable();
            $table->unsignedInteger ('break_id')->nullable();
            $table->unsignedInteger ('document_id')->nullable();
            $table->double ('debit')->nullable()->comment ('دَين');
            $table->double ('credit')->nullable()->comment ('مدين');
            $table->unsignedInteger('instalment_type_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('module_type')->nullable();
            $table->string('break_type')->nullable();
            $table->json('terms')->nullable();
            $table->integer('repate')->nullable();
            $table->date('instalment_date')->nullable();
            $table->double ('total')->nullable();
            $table->longText('details')->nullable();
            $table->unsignedInteger('installment_statu_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
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
        Schema::dropIfExists('rp_break_downs');
    }
};
