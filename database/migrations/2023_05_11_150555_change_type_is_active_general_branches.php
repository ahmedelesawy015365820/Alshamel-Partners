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
        DB::statement('UPDATE general_branches SET is_active = IF(is_active = "active", 1, 0) WHERE is_active IS NOT NULL');

        Schema::table('general_branches', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_branches', function (Blueprint $table) {
            $table->string('is_active')->default('active')->nullable()->change();
        });
        // Update the is_active column back to string
        DB::statement('UPDATE general_branches SET is_active = IF(is_active = 1, "active", "inactive") WHERE is_active IS NOT NULL');
    }
};
