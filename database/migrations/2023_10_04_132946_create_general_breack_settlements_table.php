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
        Schema::create('general_break_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id')->comment('فى جدول ال rp_break_downs هيشاور على الحقل document_id');
            $table->unsignedBigInteger('document_number')->comment('رقم سند القبض اللى جاى بواسطة ملف السيريال');
            $table->double('amount')->default(0)->comment('مبلغ التسوية المخصوم من سند القبض للبريك الحالى');
            $table->unsignedBigInteger('break_id')->comment('فى جدول ال rp_break_downs هيشاور على الحقل id {رقم القسط}');
            $table->unsignedBigInteger('break_break_id')->comment('فى جدول ال rp_break_downs هيشاور على الحقل break_id {رقم المستند اللى معملو بريك}');
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
        Schema::dropIfExists('general_breack_settlements');
    }
};
