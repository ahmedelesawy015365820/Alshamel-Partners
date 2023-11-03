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
        Schema::create('general_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('name_e', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->unsignedInteger('country_id')->nullable()->default(0);
            $table->unsignedInteger('city_id')->nullable()->default(0);
            $table->string('rp_code', 20)->nullable();
            $table->string('nationality')->nullable();
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_phone', 100)->nullable();
            $table->string('national_id', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('passport_no', 20)->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
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
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('customer_main_category_id')->nullable();
            $table->unsignedInteger('customer_sub_category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_suppliers');
    }
};
