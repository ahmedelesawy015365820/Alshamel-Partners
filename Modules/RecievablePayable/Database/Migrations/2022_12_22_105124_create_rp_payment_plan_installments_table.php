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
        Schema::create('rp_payment_plan_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger ('installment_payment_plan_id')->default (0)->comment ('from RP_InstallmentPaymentPlan');
            $table->unsignedInteger ('installment_payment_type_id')->comment ('from RP_InstallmentPaymentType');
            $table->date ('v_date')->nullable ();
            $table->date ('due_date')->nullable ();
            $table->double ('total_amount')->default (0)->comment ('اجمالي المطلوب');
            $table->double ('paid_amount')->default (0)->comment ('المسدد جزئيا');
            $table->unsignedInteger ('installment_status_id')->default (0)->comment ('from RP_InstallmentStatus');
            $table->unsignedInteger ('doc_type_id')->default (0)->comment ('from SYS_DocumentTypes');
            $table->unsignedInteger ('ref_id')->default (0)->comment ('رقم المستند (رقم عقد البيع - رقم بيع سيارة)');
            $table->string ('rp_code')->default (0)->comment ('الحساب اللي عليه اقساط');
            $table->unsignedTinyInteger ('day_month')->nullable ()->default (1)->comment ('1=Yes, 0=No (default 1)');
            $table->unsignedTinyInteger ('is_fixed')->nullable ()->default (0)->comment ('1= predefined date, 0= Undefined date (دفعة انتهاء تدريب)');
            $table->longText('note_a')->nullable();
            $table->longText('note_e')->nullable();
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
        Schema::dropIfExists('rp_payment_plan_installments');
    }
};
