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
        Schema::create('arch_document_dtls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_type_id');
            $table->unsignedBigInteger('doc_field_id');
            $table->string('field_value',1000);
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
        Schema::dropIfExists('arch_document_dtls');
    }
};
