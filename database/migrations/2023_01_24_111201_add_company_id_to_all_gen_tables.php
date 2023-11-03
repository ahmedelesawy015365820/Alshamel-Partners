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
        $pref = 'general_';
        Schema::table($pref . 'countries', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'cities', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'currencies', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'governorates', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'role_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'roles', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'role_workflows', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

        Schema::table($pref . 'employees', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'financial_years', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'units', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'avenues', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'colors', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

        Schema::table($pref . 'salesmen_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'external_salesmen', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'user_setting_screens', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'role_screen_hotfields', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'roles_workflows_buttons', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'tree_properties', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

        Schema::table($pref . 'payment_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'screen_tree_properties', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'workflows_hotfields', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'salesmen', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'internal_salesman', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'internal_salesmen', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

        Schema::table($pref . 'banks', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

        Schema::table($pref . 'bank_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
//        Schema::table($pref.config('activitylog.table_name'), function (Blueprint $table) {
//            $table->unsignedBigInteger ('company_id')->nullable ()->default (0);
//        });
        Schema::table($pref . 'customers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'data_types', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table('media', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'custom_tables', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'documents', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'departments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });
        Schema::table($pref . 'depertment_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
