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
        Schema::table('general_employee_managers', function (Blueprint $table) {
                $table->unique(['employee_id', 'manager_id', 'starting_from'], 'unique_employee_manager_date');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_employee_managers', function (Blueprint $table) {
            $table->dropUnique('unique_employee_manager_date');
        });
    }
};
