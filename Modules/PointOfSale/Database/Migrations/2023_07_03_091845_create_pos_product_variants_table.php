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
        Schema::create('pos_product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->nullable();
            $table->integer('product_id')->nullable();
            $table->string('variant_title')->nullable();
            $table->string('attribute_values')->nullable();
            $table->string('variant_details')->nullable();
            $table->double('purchase_price', 20,2)->nullable();
            $table->double('selling_price', 20,2)->nullable();
            $table->boolean('enabled')->default(1);
            $table->string('isNotify')->nullable();
            $table->string('bar_code')->nullable();
            $table->integer('re_order')->nullable();
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
        Schema::dropIfExists('pos_product_variants');
    }
};
