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
        Schema::create('rp_screen_sub_contact_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger ('sub_contract_group_id')->default (0);
            $table->unsignedInteger ('screen_id')->default (0);
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
        Schema::dropIfExists('rp_screen_sub_contact_groups');
    }
};
