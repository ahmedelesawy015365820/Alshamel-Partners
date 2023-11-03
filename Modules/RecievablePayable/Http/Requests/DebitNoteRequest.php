<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebitNoteRequest extends FormRequest
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
            'currency_id' => 'required|integer|exists:general_currencies,id',
            'customer_id' => 'required|integer|exists:general_customers,id',
            'debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'local_debit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
            'local_credit' => 'nullable|regex:/^\d+(\.\d{5,5})?$/',
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
