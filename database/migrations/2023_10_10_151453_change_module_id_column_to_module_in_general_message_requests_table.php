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
            $table->renameColumn('module_id', 'module');
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
            $table->renameColumn('module', 'module_id');
        });
    }
};
