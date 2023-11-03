<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement('UPDATE general_documents SET contusion = IF(contusion = "true", 1, 0) WHERE contusion IS NOT NULL');

        Schema::table('general_documents', function (Blueprint $table) {
            $table->boolean('contusion')->default(0)->nullable()->change();
            $table->boolean('is_default')->default(0)->nullable()->change();
            $table->boolean('required')->default(1)->nullable();
            $table->boolean('need_approve')->default(1)->nullable();
            $table->string('document_detail_type')->default('normal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_documents', function (Blueprint $table) {
            $table->string('contusion')->default('false')->change();
            $table->integer('is_default')->default(1)->change;
        });
        DB::statement('UPDATE general_documents SET contusion = IF(contusion = 1, "true", "false") WHERE contusion IS NOT NULL');
    }
};
