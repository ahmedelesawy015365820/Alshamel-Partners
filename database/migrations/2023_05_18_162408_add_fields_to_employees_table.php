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
        Schema::table('general_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->boolean('manage_others')->default(0);
            $table->renameColumn('manger_id', 'manager_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_employees', function (Blueprint $table) {

        });
    }
};
