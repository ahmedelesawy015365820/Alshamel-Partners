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
        Schema::create('general_employees', function (Blueprint $table) {
            $table->id();
            $table->string("name", 100)->nullable();
            $table->string("customer_handel")->nullable();
            $table->string("name_e", 100)->nullable();
            $table->unsignedBigInteger("department_id")->nullable();
            $table->unsignedBigInteger("salesman_type_id")->nullable();
            $table->unsignedBigInteger("manger_id")->nullable();
            $table->string('is_salesman')->default('false');
            $table->string('for_all_customer')->default('false');
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
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
        Schema::dropIfExists('general_employees');
    }
};
