<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
            'date' => 'required|date',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'number_of_years' => 'required',
            'module_type' => "nullable|string",
            'invoice_id' => "nullable|exists:rlst_invoices,id",
            'break_id' => "nullable|exists:rp_break_downs,id",
            'sponsor_id' => "nullable|exists:cm_sponsers,id",
            'branch_id' => "nullable|exists:general_branches,id",
            'document_id' => "nullable|exists:general_documents,id",
            'cm_member_id' => "nullable|exists:cm_members,id",
            'serial_number' => "nullable|string",
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after:date_from',
            'prefix' => "nullable|string",
            'type' => "nullable|string|in:subscribe,renew",
        ];
    }
}
