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
        Schema::table('general_message_requests', function (Blueprint $table) {
            $table->enum('shift_no', ['shift 1', 'shift 2', 'shift'])->comment('shift 1, shift 2 case employee has two shifts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_message_requests', function (Blueprint $table) {
            $table->dropColumn('shift_no');
        });
    }
};
