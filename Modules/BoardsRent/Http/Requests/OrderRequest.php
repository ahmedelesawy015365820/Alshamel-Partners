<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {

        return [
            "branch_id" => "required|exists:general_branches,id",
            "status_id" => "required|exists:general_statuses,id",
            "customer_id" => "required|exists:general_customers,id",
            "salesman_id" => "required|exists:general_salesmen,id",
            "sell_method_id" => "required|exists:boards_rent_sell_methods,id",
            "document_id" => "required|exists:general_documents,id",
            'date' => "required|date",
            'is_quotation' => "nullable",
            'note' => "nullable|string",
            'discount' => "nullable|numeric",
            'paid' => "nullable|numeric",
            'due' => "nullable|numeric",
            'total' => "nullable|numeric",
            'is_stripe' => "required|in:1,0",
            "serial_number" => "sometimes|unique:boards_rent_orders,serial_number," . $this->id,
            "details" => "required|array",
            "quotation_number" => "nullable|string",
            "details.*.category_id" => "required_if:is_stripe,==,0|exists:general_categories,id",
            "details.*.governorate_id" => "required_if:is_stripe,==,0|exists:general_governorates,id",
            "details.*.package_id" => "required_if:is_stripe,==,1|exists:boards_rent_packages,id",
            "details.*.quantity" => "required_if:is_stripe,==,0|numeric",
            "details.*.from" => "required|date|before:details.*.to",
            "details.*.to" => "required|date|after:details.*.from",
            "details.*.price" => "required|numeric",
            "details.*.status" => "nullable|in:true,false",
            "details.*.panels" => "required|array",
            "details.*.panels.*" => "exists:boards_rent_panels,id",
            "deleted_details.*" => "nullable|exists:boards_rent_order_details,id",
            "company_id"=>'nullable',
        ];
    }

}
