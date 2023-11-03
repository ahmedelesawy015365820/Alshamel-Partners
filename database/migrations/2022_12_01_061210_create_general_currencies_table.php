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
        Schema::create('general_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('name_e', 100)->nullable();
            $table->string('code', 15)->nullable();
            $table->string('code_e', 15)->nullable();
            $table->string('fraction', 15)->nullable();
            $table->string('fraction_e', 15)->nullable();
            $table->integer('fraction_no')->nullable();
            $table->tinyInteger('is_default')->nullable()->default(0);
            $table->tinyInteger('is_active')->nullable()->default(0);
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
        Schema::dropIfExists('general_currencies');
    }
};
