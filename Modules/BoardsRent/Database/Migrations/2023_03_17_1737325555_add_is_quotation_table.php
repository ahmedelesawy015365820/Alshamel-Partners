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
        // add is_quotation
        Schema::table('boards_rent_orders', function ($table) {
           $table->boolean('is_quotation')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove is_quotation
        Schema::table('boards_rent_orders', function ($table) {
            $table->dropColumn('is_quotation');
        });

    }
};
