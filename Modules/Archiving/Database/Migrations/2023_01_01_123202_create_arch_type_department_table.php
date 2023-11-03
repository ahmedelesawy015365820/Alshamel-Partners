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
        Schema::create('arch_type_department', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arch_doc_type_id');
            $table->unsignedBigInteger('arch_department_id');
            $table->unsignedBigInteger("parent_id")->nullable();
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
        Schema::dropIfExists('arch_document_department');
    }
};
