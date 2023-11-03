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
        Schema::create('rlst_unit_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_e', 100);
            $table->tinyInteger('is_default')->default(0);
            $table->string("is_active")->default('active');
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
        Schema::dropIfExists('rlst_unit_statuses');
    }
};
