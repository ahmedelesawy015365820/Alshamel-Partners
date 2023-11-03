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
        Schema::table('general_customers', function (Blueprint $table) {
            $table->unsignedInteger('customer_main_category_id')->nullable();
            $table->unsignedInteger('customer_sub_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_customers', function (Blueprint $table) {
            //
        });
    }
};
