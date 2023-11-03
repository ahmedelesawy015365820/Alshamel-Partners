<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstContractRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "date" => "sometimes|date",
            "serial_id" => "sometimes|exists:general_serials,id",
            "serial_number" => "sometimes|unique:rlst_reservations,serial_number," . $this->id,
            "salesman_id" => "sometimes|exists:general_salesmen,id,deleted_at,NULL",
            'document_id' => 'nullable|exists:general_documents,id',
            "customer_id" => "sometimes|exists:general_customers,id",
            "branch_id" => ["sometimes", "exists:general_branches,id", new \App\Rules\BranchSerialExistRule()],
            "start_date" => "sometimes|date",
            "end_date" => "sometimes|date|after_or_equal:start_date",
            "paid_amount" => 'numeric|min:0',
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            "details" => "required|array",
            "details.*.building_id" => "required_with:details|exists:rlst_buildings,id,deleted_at,NULL",
            "details.*.unit_id" => "required_with:details|exists:rlst_units,id,deleted_at,NULL",
            "details.*.installment_payment_plans_id" => "required_with:details|exists:rp_installment_payment_plans,id",
            'details.*.reservation_value' => 'numeric|min:0',
            'details.*.unit_value' => 'numeric|min:0',
            "company_id"=>'nullable',

        ];
    }

}
