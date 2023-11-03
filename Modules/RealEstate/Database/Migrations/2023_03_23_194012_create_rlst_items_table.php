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
        Schema::create('rlst_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('name_e')->unique();
            $table->string('code_number');
            $table->string('type')->default('service')->nullable();
            $table->foreignId('unit_id')->nullable();
            $table->double('price');
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
        Schema::dropIfExists('rlst_items');
    }
};
