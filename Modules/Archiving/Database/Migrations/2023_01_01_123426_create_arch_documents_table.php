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
        Schema::create('arch_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arch_doc_type');
            $table->unsignedBigInteger('doc_status');
            $table->text('doc_description');
            $table->string("url_reference", 200);
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
        Schema::dropIfExists('arch_documents');
    }
};
