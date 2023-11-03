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
        Schema::create('cm_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('Entry');

            $table->unsignedInteger('branch_id')->comment('Entry');
            $table->unsignedInteger('serial_id')->comment('Entry');

            $table->unsignedInteger('document_no')->nullable()->comment('Entry');
            $table->double('amount')->default(0)->comment('Display');

            $table->unsignedInteger('sponsor_id')->nullable()->comment('Display'); // NULLABLE
            $table->string('notes')->nullable(); // NULLABLE
            $table->unsignedInteger('member_number')->nullable()->comment('Display, ZERO case applying'); // NULLABLE
            $table->unsignedInteger('cm_member_id')->nullable()->comment('Entry');
            $table->year('year')->nullable();

            $table->unsignedInteger("document_id")->comment('NoEntry, NoDisplay');
            $table->string('serial_number')->nullable()->comment('Entry');
            $table->string('prefix')->nullable()->comment('Entry'); // NULLABLE
            $table->string('type')->comment('(Display)subscribe,renew');

            $table->unsignedSmallInteger('year_from')->comment('Display');
            $table->unsignedSmallInteger('year_to')->comment('Display');

            $table->unsignedTinyInteger('number_of_years')->default(1)->comment('Display, max: 255 years');
            $table->unsignedInteger('invoice_id')->nullable(); // NULLABLE ???
            $table->unsignedInteger('break_id')->nullable(); // NULLABLE ???

            $table->string('old_doc')->nullable();
            $table->unsignedInteger('financial_year_id')->nullable();
            $table->unsignedInteger('member_request_id')->nullable();

            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();


            $table->unsignedInteger('company_id')->default(0);

            $table->unsignedInteger('created_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('cm_transactions');
    }
};
