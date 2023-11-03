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
        Schema::create('rlst_contracts', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->foreignId('salesman_id');
            $table->foreignId('customer_id');
            $table->foreignId('branch_id');
            $table->foreignId('document_id')->nullable();
            $table->foreignId('serial_id')->nullable();
            $table->date("start_date");
            $table->date("end_date");
            $table->integer("serial_number")->nullable();
            $table->string("prefix")->nullable();

            $table->string('module_type')->nullable();
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
        Schema::dropIfExists('rlst_contracts');
    }
};
