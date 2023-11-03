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
        Schema::create('arch_document_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment("Name Arabic")->unique();
            $table->string('name_e')->comment("Name English")->unique();
            $table->string('data_type_id')->nullable();
            $table->integer('is_reference')->default(0);
            $table->string('lookup_table')->nullable();
            $table->string("lookup_table_column")->nullable();
            $table->unsignedBigInteger('tree_property_id')->nullable();
            $table->string('field_characters')->nullable();
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
        Schema::dropIfExists('arch_document_fields');
    }
};
