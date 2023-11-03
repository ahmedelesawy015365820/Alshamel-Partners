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
        Schema::table('general_customers', function (Blueprint $table) {
            $table->dropColumn('note1');
            $table->dropColumn('note2');
            $table->dropColumn('note3');
            $table->dropColumn('note4');
            $table->string('note')->nullable();
            $table->string('code_country')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('twitter')->nullable();
            $table->string('website')->nullable();
            $table->unsignedInteger('salesman_id')->nullable();
            $table->unsignedInteger('sector_id')->nullable();
            $table->unsignedInteger('customer_source_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_customers', function (Blueprint $table) {
            //
        });
    }
};
