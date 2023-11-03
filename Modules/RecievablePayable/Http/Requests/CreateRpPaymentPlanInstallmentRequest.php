<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateRpPaymentPlanInstallmentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "payment_plan_installments"  => "required|array",
            'payment_plan_installments.*.installment_payment_plan_id' => 'required',
            'payment_plan_installments.*.installment_payment_type_id' => 'required',
            'payment_plan_installments.*.v_date' => 'required',
            'payment_plan_installments.*.due_date' => 'required',
            'payment_plan_installments.*.total_amount' => 'required',
            'payment_plan_installments.*.paid_amount' => 'required',
            'payment_plan_installments.*.installment_status_id' => 'required',
            'payment_plan_installments.*.doc_type_id' => 'required',
            'payment_plan_installments.*.ref_id' => 'required',
            'payment_plan_installments.*.rp_code' => 'required',
            'payment_plan_installments.*.is_fixed' => [],
            'payment_plan_installments.*.day_month' => [],
            'payment_plan_installments.*.note_a' => 'nullable|string',
            'payment_plan_installments.*.note_e' => 'nullable|string',
            "company_id"=>'nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

}
