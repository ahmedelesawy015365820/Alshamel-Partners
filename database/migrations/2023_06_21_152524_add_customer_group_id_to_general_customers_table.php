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
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->boolean('is_supplier')->default(0);
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
