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
        Schema::table('general_voucher_headers', function (Blueprint $table) {
            $table->unsignedBigInteger('module_type_id')->nullable()->comment('general_document_module_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_voucher_headers', function (Blueprint $table) {
            //
        });
    }
};
