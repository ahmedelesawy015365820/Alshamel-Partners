<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "company_id"=>'nullable',
            'transactions' => 'required|array',
            'transactions.*.date' => 'required|date',
            'transactions.*.amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'transactions.*.number_of_years' => 'required',
            'transactions.*.module_type' => "nullable|string",
            'transactions.*.invoice_id' => "nullable|exists:rlst_invoices,id",
            'transactions.*.break_id' => "nullable|exists:rp_break_downs,id",
            'transactions.*.sponsor_id' => "nullable|exists:cm_sponsers,id",
            'transactions.*.branch_id' => "nullable|exists:general_branches,id",
            'transactions.*.document_id' => "nullable|exists:general_documents,id",
            'transactions.*.serial_id' => "nullable|exists:general_serials,id",
            'transactions.*.cm_member_id' => "nullable|exists:cm_members,id",
            'transactions.*.serial_number' => "nullable|string",
            'transactions.*.date_from' => 'nullable|date|before_or_equal:transactions.*.date_to',
            'transactions.*.date_to' => 'nullable|date|after:transactions.*.date_from',
            'transactions.*.prefix' => "nullable|string",
            'transactions.*.type' => "required|string|in:subscribe,renew",
        ];
    }
}
