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
        Schema::create('hr_online_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_type_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('start_from');
            $table->date('end_date');
            $table->string('descriptions', 100);
            $table->double('amount');
            $table->time('from_hour');
            $table->time('to_hour');
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
        Schema::dropIfExists('hr_online_requests');
    }
};
