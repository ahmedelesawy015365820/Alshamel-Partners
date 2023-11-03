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
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->string('type')->default('general');
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->unsignedBigInteger('customer_id')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('equipment_id');
            $table->dropColumn('customer_id');
        });
    }
};
