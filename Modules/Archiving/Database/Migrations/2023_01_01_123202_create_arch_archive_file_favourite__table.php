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
        Schema::create('arch_archive_file_favourite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arch_archive_file_id');
            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger("admin_id")->nullable();
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
        Schema::dropIfExists('arch_archive_file_favourite');
    }
};
