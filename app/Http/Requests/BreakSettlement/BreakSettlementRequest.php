<?php

namespace App\Http\Requests\BreakSettlement;

use Illuminate\Foundation\Http\FormRequest;

class BreakSettlementRequest extends FormRequest
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
            'break_settlement.*.voucher_headers_id'     => 'nullable|exists:general_voucher_headers,id',
            'break_settlement.*.amount'          => 'required',
            'break_settlement.*.break_id'        => 'nullable|exists:rp_break_downs,id',
            'break_settlement.*.document_id'        => 'nullable|exists:general_documents,id',
            'break_settlement.*.affect'        => 'nullable|int:1,-1',
        ];
    }
}
