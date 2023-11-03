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
        Schema::table('hr_online_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable();
            $table->date('approved_date')->default(now());
            $table->date('approved_from_date')->default(now());
            $table->date('approved_to_date')->default(now());
            $table->time('approved_from_hour')->default(now()->format('H:i:s'));
            $table->time('approved_to_hour')->default(now()->format('H:i:s'));
            $table->double('approved_amount')->default(0);
            $table->date('date')->default(now());
            $table->string('admin_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_online_requests', function (Blueprint $table) {
            //
        });
    }
};
