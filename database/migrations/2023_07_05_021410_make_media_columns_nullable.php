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
        Schema::table('media', function (Blueprint $table) {
            $table->string('collection_name')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
            $table->string('disk')->nullable()->change();
            $table->string('conversions_disk')->nullable()->change();
            $table->unsignedBigInteger('size')->nullable()->change();
            $table->json('manipulations')->nullable()->change();
            $table->json('custom_properties')->nullable()->change();
            $table->json('generated_conversions')->nullable()->change();
            $table->json('responsive_images')->nullable()->change();
            $table->unsignedInteger('order_column')->nullable()->change();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            //
        });
    }
};
