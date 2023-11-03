<?php

use Illuminate\Database\Migrations\Migration;
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

        //  add columns facebook , instagram , linkedin , snapchat ,twitter , salesman_id , customer_source_id
        Schema::table('general_customers', function ($table) {
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('twitter')->nullable();
            $table->string('website')->nullable();
            $table->unsignedInteger('salesman_id')->nullable();
            $table->unsignedInteger('customer_source_id')->nullable();
            $table->unsignedInteger('sector_id')->nullable();
            $table->unsignedInteger('avenue_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //  drop columns facebook , instagram , linkedin , snapchat ,twitter , salesman_id , customer_source_id
        Schema::table('general_customers', function ($table) {
            $table->dropColumn('facebook');
            $table->dropColumn('instagram');
            $table->dropColumn('linkedin');
            $table->dropColumn('snapchat');
            $table->dropColumn('twitter');
            $table->dropColumn('website');
            $table->dropColumn('salesman_id');
            $table->dropColumn('customer_source_id');
            $table->dropColumn('customer_source_id');
            $table->dropColumn('sector_id');
            $table->dropColumn('avenue_id');


        });
    }
};
