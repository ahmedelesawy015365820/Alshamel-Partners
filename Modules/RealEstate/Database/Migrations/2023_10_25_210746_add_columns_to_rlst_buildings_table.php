<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rlst_buildings', function (Blueprint $table) {
            $table->unsignedInteger('building_type_id');
            $table->boolean('company_ownership')->default(1)->comment('if yes: owned by the company, if no: owned by outside-owner');
            
            // for property components (المكونات العقارية)
            $table->integer('floors_number');
            $table->integer('vaults_number');
            $table->integer('ground_floors_number');
            $table->integer('mediums_number')->comment('this should be like two-level or more flats in the building');
            $table->integer('elevators_number');
            $table->integer('electricity_meters_number');
            $table->integer('water_meters_number');
            $table->integer('gas_meters_number');
            $table->boolean('central_air_conditioning')->default(1);

            // for financial details
            $table->double('buying_price');
            $table->double('buying_date');
            $table->double('middleman_cost');
            $table->double('registration_cost');
            $table->unsignedInteger('building_currency_id');

            // for accounting details

            $table->unsignedInteger('accrued_revenues_account_id');
            $table->unsignedInteger('advance_revenues_account_id');
            $table->unsignedInteger('revenues_account_id');
            $table->unsignedInteger('discounts_account_id');
            $table->unsignedInteger('cash_account_id');
            $table->unsignedInteger('knet_account_id');
            $table->unsignedInteger('insurance_account_id');
            $table->unsignedInteger('main_cost_center_id');
            $table->enum('financial_period', ['monthly', 'yearly'])->default('monthly');


            //AFTER EZZAT FINISHES 
            //$table->foreign('building_currency_id')->references('id')->on('general_currencies');
            //$table->foreign('accrued_revenues_account_id')->references('id')->on('general_accounts');
            //$table->foreign('advance_revenues_account_id')->references('id')->on('general_accounts');
            //$table->foreign('revenues_account_id')->references('id')->on('general_accounts');
            //$table->foreign('discounts_account_id')->references('id')->on('general_accounts');
            //$table->foreign('cash_account_id')->references('id')->on('general_accounts');
            //$table->foreign('knet_account_id')->references('id')->on('general_accounts');
            //$table->foreign('insurance_account_id')->references('id')->on('general_accounts');
            //$table->foreign('main_cost_center_id')->references('id')->on('general_cost_centers');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('rlst_buildings', function (Blueprint $table) {
            $table->dropColumn([
                'building_type_id',
                'company_ownership',
                'floors_number',
                'vaults_number',
                'ground_floors_number',
                'mediums_number',
                'elevators_number',
                'electricity_meters_number',
                'water_meters_number',
                'gas_meters_number',
                'central_air_conditioning',
                'buying_price',
                'buying_date',
                'middleman_cost',
                'registration_cost',
                'building_currency_id',
                'accrued_revenues_account_id',
                'advance_revenues_account_id',
                'revenues_account_id',
                'discounts_account_id',
                'cash_account_id',
                'knet_account_id',
                'insurance_account_id',
                'main_cost_center_id',
                'financial_period',
            ]);

        $table->dropForeign(['building_currency_id']);
        $table->dropForeign(['accrued_revenues_account_id']);
        $table->dropForeign(['advance_revenues_account_id']);
        $table->dropForeign(['revenues_account_id']);
        $table->dropForeign(['discounts_account_id']);
        $table->dropForeign(['cash_account_id']);
        $table->dropForeign(['knet_account_id']);
        $table->dropForeign(['insurance_account_id']);
        $table->dropForeign(['main_cost_center_id']);


        }); // schema

    } // down
};
