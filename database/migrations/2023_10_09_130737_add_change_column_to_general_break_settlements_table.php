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
            $table->unsignedBigInteger('voucher_header_id');
            $table->dropColumn('document_id');
            $table->dropColumn('document_number');
            $table->dropColumn('break_break_id');
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
