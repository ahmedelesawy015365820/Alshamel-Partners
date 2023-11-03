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
        Schema::create('general_message_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->date('message_date');
            $table->integer('message_type_id');
            $table->unsignedSmallInteger('request_mins');
            $table->unsignedTinyInteger('module_id');
            $table->enum('status', ['processed', 'not processed'])->default('not processed')->comment('processed: msg is sent, not processed: msg is NOT sent yet');
            $table->timestamps();

            // $table->foreign('employee_id')->references('id')->on('general_employees');
            // $table->foreign('message_type_id')->references('id')->on('general_message_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_message_requests');
    }
};