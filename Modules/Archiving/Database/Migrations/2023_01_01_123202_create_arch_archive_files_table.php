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
        Schema::create('arch_archive_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arch_department_id')->nullable();
            $table->foreignId('arch_doc_type_id');
            $table->json('data_type_value');
            $table->unsignedBigInteger("user_id")->nullable();
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
        Schema::dropIfExists('arch_archive_files');
    }
};
