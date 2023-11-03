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
        Schema::create('rp_sub_contact_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('rp_main_contact_group_id')->nullable()->default (0);
            $table->string ('name')->nullable ();
            $table->string ('name_e')->nullable ();
            $table->unsignedTinyInteger('gl_acc_no')->nullable()->default (0);
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
        Schema::dropIfExists('rp_sub_contact_groups');
    }
};
