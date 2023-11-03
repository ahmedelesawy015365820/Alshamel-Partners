<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('hr_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('request_types_id');
            $table->dateTime('request_datetime')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'))->useCurrent();
            $table->date('from_date');
            $table->date('to_date');
            $table->string('remark');
            $table->double('amount');
            $table->time('from_hour');
            $table->time('to_hour');
            $table->unsignedBigInteger('request_status_id')->default(1);
            $table->date('approved_from_date')->nullable();
            $table->date('approved_to_date')->nullable();
            $table->time('approved_from_hour')->nullable();
            $table->time('approved_to_hour')->nullable();
            $table->double('approved_amount')->nullable();
            $table->date('approved_date')->nullable();
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
        Schema::dropIfExists('requests');
    }
};
