<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transactions' => 'required|array',
            'transactions.*.date' => 'required|date',
            'transactions.*.amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'transactions.*.number_of_years' => 'nullable',
            'transactions.*.year' => 'nullable|digits:4',
            'transactions.*.date_from' => 'nullable|date',
            'transactions.*.date_to' => 'nullable|date',
            'transactions.*.invoice_id' => "nullable|exists:rlst_invoices,id",
            'transactions.*.break_id' => "nullable|exists:rp_break_downs,id",
            'transactions.*.sponsor_id' => "nullable|exists:cm_sponsers,id",
            'transactions.*.member_request_id' => "nullable|exists:cm_member_requests,id",
            'transactions.*.branch_id' => "nullable|exists:general_branches,id",
            'transactions.*.document_id' => "nullable|exists:general_documents,id",
            'transactions.*.serial_id' => "nullable|exists:general_serials,id",
            'transactions.*.cm_member_id' => "nullable|exists:cm_members,id",
            'transactions.*.serial_number' => "nullable|string",
            'transactions.*.year_from' => 'required',
            'transactions.*.year_to' => 'required|gte:transactions.*.year_from',
            'transactions.*.prefix' => "nullable|string",
            'transactions.*.type' => "required|string|in:subscribe,renew",
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
