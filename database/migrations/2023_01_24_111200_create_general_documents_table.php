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
        Schema::create('general_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment("Name Arabic");
            $table->string('name_e')->nullable()->comment("Name english");
            $table->json('attributes')->nullable();
            $table->foreignId('branche_id')->nullable();
            $table->foreignId('serial_id')->nullable();
            $table->integer('is_default')->default(1);
            $table->unsignedTinyInteger('is_admin')->default(0)->comment('default(1) = documents from table  sys_document_company_modules default(0)');
            $table->string('contusion')->default('false');
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
        Schema::dropIfExists('general_documents');
    }
};
