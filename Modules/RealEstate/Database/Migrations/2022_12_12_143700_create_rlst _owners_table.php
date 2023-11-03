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
        Schema::create('rlst_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('name_e', 100);
            $table->string('phone', 20);
            $table->string('email', 100)->nullable();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->string("rb_code", 20);
            $table->string("phone_code", 20);
            $table->unsignedBigInteger("nationality_id");
            $table->unsignedBigInteger('bank_account_id');
            $table->string("contact_person", 100)->nullable();
            $table->string("contact_phones", 100)->nullable();
            $table->string("national_id", 20);
            $table->string("whatsapp", 20)->nullable();
            $table->text('categories')->nullable();
            $table->text('attachments')->nullable();
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
        Schema::dropIfExists('rlst_owners');
    }
};
