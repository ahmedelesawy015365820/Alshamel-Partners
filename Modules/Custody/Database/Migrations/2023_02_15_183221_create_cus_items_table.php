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
        Schema::create('cus_items', function (Blueprint $table) {
            $table->id();
            $table->string("name", 255);
            $table->string("name_e", 255);
            $table->string("status")->comment('for repeat no limited and unlimited');
            $table->string("default_amount");
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('creation_user_id');
            $table->unsignedBigInteger('last_update_user_id')->nullable();
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
        Schema::dropIfExists('cus_items');
    }
};
