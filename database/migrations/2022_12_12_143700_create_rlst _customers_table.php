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
        Schema::create('rlst_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_e', 100);
            $table->string('phone', 20);
            $table->string('email', 100);
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->string("rb_code", 20);
            $table->unsignedBigInteger("nationality_id");
            $table->unsignedBigInteger('bank_account_id');
            $table->string("contact_person", 100);
            $table->string("contact_phones", 100);
            $table->string("national_id", 20);
            $table->string('passport_no',20);
            $table->string("whatsapp", 20);
            $table->string('note1',100);
            $table->string('note2',100);
            $table->string('note3',100);
            $table->string('note4',100);
            $table->json('categories')->nullable();
            $table->json('attachments')->nullable();
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
        Schema::dropIfExists('rlst_customers');
    }
};
