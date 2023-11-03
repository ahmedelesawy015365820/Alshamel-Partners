<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOpeningBalanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "opening_balances"  => "required|array",
            'opening_balances.*.date' => 'required|date',
            'opening_balances.*.rate' => 'required|regex:/^\d+(\.\d{2,2})?$/',
            'opening_balances.*.currency_id' => 'required|integer',
            'opening_balances.*.customer_id' => 'required|integer',
            'opening_balances.*.debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'opening_balances.*.credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'opening_balances.*.local_debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'opening_balances.*.local_credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'opening_balances.*.client_type_id' => 'nullable|exists:general_client_types,id',
            'opening_balances.*.module_type_id' => 'nullable',
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
