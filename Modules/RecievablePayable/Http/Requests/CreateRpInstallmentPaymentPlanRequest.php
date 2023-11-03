<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateRpInstallmentPaymentPlanRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:rp_installment_payment_plans,name',
            'name_e' => 'required|unique:rp_installment_payment_plans,name_e',
            'is_default' => 'required',
            'is_active' => [],
            'description' => [],
            'description_e' => [],
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
