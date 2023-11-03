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
        Schema::table('general_messages', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->unsignedBigInteger('message_type_id')->nullable();
            $table->string('module')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_messages', function (Blueprint $table) {
            $table->integer('type')->comment((' رسالة تأخير / رسالة اضافة / رسالة غياب'));
        });
    }
};
