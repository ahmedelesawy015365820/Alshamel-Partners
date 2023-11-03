<?php

namespace Modules\RecievablePayable\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CreateRpInstallmentPaymentTypeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:rp_installment_payment_types,name',
            'name_e' => 'required|string|max:255|unique:rp_installment_payment_types,name_e',
            'step' => 'nullable|string|max:255',
            'is_conditional' => [],
            'installment_condation_id' => ['required_if:is_conditional,==,1'],
            "installment_payment_type_freq" => '',
            'is_partially' => [],
            'is_passed' => [],
            'is_passed_all' => [],
            'is_passed_contract_plan' => [],
            'auto_freq' => ['required_if:installment_payment_type_freq.*,>=,1' ],
            'freq_period' => 'nullable',
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
