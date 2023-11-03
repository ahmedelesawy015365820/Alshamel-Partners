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
        Schema::create('general_document_approval_details', function (Blueprint $table) {
            $table->id();
            $table->string('document_serial_number');
            $table->text('notes')->nullable();
            $table->time('approval_time')->nullable();
            $table->date('decision_date')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('decision_id')->nullable()->comment('from general_documents_statuses');
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
        Schema::dropIfExists('general_document_approval_details');
    }
};
