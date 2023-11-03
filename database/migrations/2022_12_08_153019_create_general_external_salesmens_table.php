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
        Schema::create('general_external_salesmen', function (Blueprint $table) {
            $table->id();
            $table->string('phone', '20')->nullable();
            $table->string("address", 255)->nullable();
            $table->string('rp_code', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('is_active')->default('active');
            $table->integer('national_id')->nullable();
            $table->bigInteger('country_id')->nullable();
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
        Schema::dropIfExists('general_external_salesmen');
    }
};
