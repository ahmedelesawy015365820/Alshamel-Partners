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
        Schema::create('pos_cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_e')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('created_by')->nullable();
            $table->boolean('multiple_access')->nullable();
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
        Schema::dropIfExists('pos_cash_registers');
    }
};
