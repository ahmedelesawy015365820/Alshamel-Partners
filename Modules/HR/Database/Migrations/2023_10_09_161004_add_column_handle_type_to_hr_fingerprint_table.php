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
        Schema::table('hr_fingerprint', function (Blueprint $table) {
            $table->enum('handle_type', ['N', 'H', 'D', 'O'])->default('N')->comment('N: not handled, H: handled w/o msg D: delay msg, O: overtime msg');
        });

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_fingerprint', function (Blueprint $table) {
            $table->dropColumn('handle_type');
        });


    }
};
