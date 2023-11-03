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
        Schema::table('general_break_settlements', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable();
            $table->integer('affect')->nullable();
            $table->unsignedBigInteger('break_id')->nullable()->change();
            $table->unsignedBigInteger('voucher_header_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_break_settlements', function (Blueprint $table) {
            //
        });
    }
};
