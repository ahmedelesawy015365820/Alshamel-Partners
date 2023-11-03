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
            $table->dropColumn('facebook');
            $table->dropColumn('instagram');
            $table->dropColumn('linkedin');
            $table->dropColumn('snapchat');
            $table->dropColumn('twitter');
            $table->dropColumn('website');
            $table->dropColumn('salesman_id');
            $table->dropColumn('customer_source_id');
            $table->dropColumn('sector_id');
            $table->dropColumn('avenue_id');
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
