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
        Schema::table('hr_requests', function (Blueprint $table) {
            $table->date('from_date')->nullable()->change();
            $table->date('to_date')->nullable()->change();
            $table->string('remark')->nullable()->change();
            $table->double('amount')->nullable()->change();
            $table->time('from_hour')->nullable()->change();
            $table->time('to_hour')->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_requests', function (Blueprint $table) {
            $table->date('from_date')->change();
            $table->date('to_date')->change();
            $table->string('remark')->change();
            $table->double('amount')->change();
            $table->time('from_hour')->change();
            $table->time('to_hour')->change();
        });
    }
};
