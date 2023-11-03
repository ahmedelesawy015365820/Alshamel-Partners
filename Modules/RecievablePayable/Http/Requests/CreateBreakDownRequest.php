<?php

namespace Modules\RecievablePayable\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBreakDownRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'break_downs' => 'required|array',
            'break_downs.*.id' => 'nullable',
            'break_downs.*.instalment_date' => 'nullable|date',
            'break_downs.*.rate' => 'nullable',
            'break_downs.*.currency_id' => 'nullable|integer',
            'break_downs.*.document_id' => 'nullable|integer',
            'break_downs.*.customer_id' => 'nullable|integer',
            'break_downs.*.invoice_id' => 'nullable|integer',
            'break_downs.*.break_id' => 'required|integer',
            'break_downs.*.break_type' => 'nullable|string',
            'break_downs.*.instalment_type_id' => 'nullable|integer',
            'break_downs.*.parent_id' => 'nullable|integer',
            'break_downs.*.client_type_id' => 'nullable|exists:general_client_types,id',
            'break_downs.*.module_type' => 'nullable|string',
            'break_downs.*.debit' => 'nullable',
            'break_downs.*.credit' => 'nullable',
            'break_downs.*.repate' => 'required|integer',
            'break_downs.*.total' => 'required',
            "break_downs.*.terms"  => "nullable|array",
            'break_downs.*.installment_statu_id' => 'nullable|integer',
            'break_downs.*.details' => 'nullable|string',
            'break_downs.*.amount_status' => 'nullable|string|in:Paid_Partially,Paid',
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
