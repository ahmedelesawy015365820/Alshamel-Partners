<?php

namespace App\Http\Requests\VoucherHeader;

use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherHeaderRequest extends FormRequest
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

            'document_id'             => 'required|integer|exists:general_documents,id',
            'branch_id'               => 'required|integer|exists:general_branches,id',
            'date'                    => 'nullable|date',
            'serial_id'               => 'nullable',
            'salesmen_id'             => 'required|integer|exists:general_employees,id',
            'customer_id'             => 'required|integer|exists:general_customers,id',
            'client_type_id'          => 'nullable|integer|exists:general_client_types,id',
            'payment_method_id'       => 'nullable',
            'module_type_id'          => 'required|integer|exists:general_document_module_types,id',
            'amount'                  => 'required|numeric',

//            'break_settlement.*.voucher_header_id'     => 'required|exists:general_voucher_headers,id',
            'break_settlement.*.amount'          => 'required',
            'break_settlement.*.break_id'        => 'required|exists:rp_break_downs,id',

        ];
    }
}
