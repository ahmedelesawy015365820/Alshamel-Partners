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
        Schema::create('general_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('task_title')->nullable();
            $table->date('execution_date')->nullable();
            $table->string('execution_duration')->nullable();
            $table->date('execution_end_date')->nullable();
            $table->date('notification_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedBigInteger('department_task_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->tinyInteger('is_closed')->default(0);
            $table->text('admin_note')->nullable();
            $table->text('task_requirement')->nullable();
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
        Schema::dropIfExists('general_tasks');
    }
};
