<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Nullify the columns in the rlst_building_policies table.
        Schema::table('rlst_building_policies', function (Blueprint $table) {
            
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN after_expenses ENUM('yes', 'no') DEFAULT NULL");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN plus_extra_revenues ENUM('yes', 'no') DEFAULT NULL");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN collected_rent_type ENUM('actual', 'accrued') DEFAULT NULL");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN company_pays_rest ENUM('yes', 'no') DEFAULT NULL");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN owner_pays_rest ENUM('yes', 'no') DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rlst_building_policies', function (Blueprint $table) {

            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN after_expenses ENUM('yes', 'no') DEFAULT 'yes' "); 
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN plus_extra_revenues ENUM('yes', 'no') DEFAULT 'yes' ");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN collected_rent_type ENUM('actual', 'accrued') DEFAULT 'actual' ");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN company_pays_rest ENUM('yes', 'no') DEFAULT 'yes' ");
            DB::statement("ALTER TABLE rlst_building_policies MODIFY COLUMN owner_pays_rest ENUM('yes', 'no') DEFAULT 'yes' ");
        });
    }
};
