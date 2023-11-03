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
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_e')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_e')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->tinyInteger('taxable')->default(0);
            $table->string('tax_type')->nullable();
            $table->string('tax_id')->nullable();
            $table->integer('is_quantity')->nullable();
            $table->string('product_type')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('pos_products');
    }
};
