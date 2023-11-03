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
        Schema::create('gl_coas', function (Blueprint $table) {
            $table->id();
            $table->string ('acc_no',20)->comment ('رقم الحساب');
            $table->string ('name',100)->comment ('اسم الحساب');
            $table->string ('name_e',100)->comment ('اسم الحساب انجليزي');
            $table->string ('parent_no',20)->comment ('الحساب الرئيسي المباشر');
            $table->unsignedInteger ('accounttype_id')->comment ('required from GL_AccountTypes');
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
        Schema::dropIfExists('gl_coas');
    }
};
