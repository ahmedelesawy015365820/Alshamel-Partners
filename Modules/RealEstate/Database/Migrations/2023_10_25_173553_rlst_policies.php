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
        Schema::create('rlst_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('name_e', 50);
            $table->string('description', 255);
            $table->boolean('after_expenses')->default(1)->comment('بعد النفقات ام لا');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rlst_policies');
    }
};
