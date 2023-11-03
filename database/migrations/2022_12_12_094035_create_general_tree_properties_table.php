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
        Schema::create('general_tree_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('name_e', 100)->nullable();
            $table->unsignedInteger('parent_id')->nullable()->default(0);
            //            $table->unsignedInteger ('screen_id')->nullable ()->default (0);
            $table->unsignedTinyInteger('required')->nullable()->default(0);
            //            $table->string ('screen_node')->nullable ();
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
        Schema::dropIfExists('general_tree_properties');
    }
};
