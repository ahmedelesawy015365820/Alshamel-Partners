<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditOpeningBalanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'rate' => 'required|regex:/^\d+(\.\d{2,2})?$/',
            'currency_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'local_debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'local_credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'module_type_id' => 'nullable',

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
