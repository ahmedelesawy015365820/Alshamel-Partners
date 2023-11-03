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
        Schema::create('rp_installment_payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_e', 100);
            $table->unsignedTinyInteger('is_conditional')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('installment_condation_id')->nullable();
            $table->unsignedTinyInteger('installment_payment_type_freq')->nullable()->default(1)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('is_partially')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('is_passed')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('is_passed_all')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('is_passed_contract_plan')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->unsignedTinyInteger('auto_freq')->nullable()->default(0)->comment('1=Yes, 0=No (default 0)');
            $table->integer('freq_period')->nullable();
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
        Schema::dropIfExists('rp_installment_payment_types');
    }
};
