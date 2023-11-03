<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_serials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('start_no')->nullable()->comment('Op code (1=Add,2= Update, 3=Delete 4=View, ….)');
            $table->string('perfix')->nullable();
            $table->string('suffix')->nullable();
            $table->string('restart_period')->default(0)->comment('Daily, monthly, yearly, open');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('is_default')->default(0)->comment('1=Yes, 0=No');
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
        Schema::dropIfExists('general_serials');
    }
}
