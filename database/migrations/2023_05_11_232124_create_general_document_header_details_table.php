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
        Schema::create('general_document_header_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_header_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->unsignedInteger('governorate_id')->nullable();
            $table->unsignedInteger('package_id')->nullable();
            $table->bigInteger('rent_days')->default(0);
            $table->string('unit_type')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->double('price_per_uint');
            $table->boolean('is_stripe')->default(0);
            $table->double('total')->default(0);
            $table->double('invoice_discount')->default(0);
            $table->double('net_invoice')->default(0);
            $table->double('unrealized_revenue')->default(0);
            $table->double('external_commission')->default(0);
            $table->double('revenue')->default(0);
            $table->double('unrealized_commission')->default(0);
            $table->double('commission')->default(0);
            $table->double('sell_method_discount')->default(0);
            $table->unsignedInteger('sell_method_id')->nullable();
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
        Schema::dropIfExists('general_document_header_details');
    }
};
