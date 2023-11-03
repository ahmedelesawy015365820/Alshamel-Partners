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
        Schema::create('cm_member_rejects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('serial_id')->nullable();
            $table->unsignedInteger('cm_member_id');
            $table->unsignedInteger('cm_member_type_id');
            $table->unsignedInteger("document_id");
            $table->date('date');
            $table->text('note');
            $table->string('session_number')->nullable();
            $table->string('prefix')->nullable();
            $table->unsignedInteger('discharge_reson_id')->nullable();
            $table->string('entity', 20)->nullable();
            
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
        Schema::dropIfExists('cm_member_rejects');
    }
};
