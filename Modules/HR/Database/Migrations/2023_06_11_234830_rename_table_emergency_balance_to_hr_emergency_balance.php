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
        Schema::table('emergency_balance', function (Blueprint $table) {
            Schema::rename('emergency_balance', 'hr_emergency_balance');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emergency_balance', function (Blueprint $table) {
            Schema::rename('hr_emergency_balance', 'emergency_balance');

        });
    }
};
