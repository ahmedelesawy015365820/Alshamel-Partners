<?php

namespace Modules\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrederRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "sales_note"                    => 'nullable|string',
            "all_discount"                  => 'nullable|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'customer_id'                   => 'nullable|exists:general_customers,id',
            "sub_discount"                  => 'nullable|',
            'branch_id'                     => 'required|exists:general_branches,id',
            'document_id'                   => 'required|exists:general_documents,id',
            'order_id'                      => 'nullable|exists:pos_orders,id',
            'returned_invoice'              => 'nullable|string',
            'tax_type'                      => 'nullable|in:included,excluded',
            'order_return_type'             => 'nullable|string',
            'created_by'                    => 'required|string',
            'products.*.product_id'         => 'nullable|exists:pos_products,id',
            'products.*.product_variant_id' => 'nullable|exists:pos_product_variants,id',
            'products.*.tax_id'             => 'nullable|exists:general_taxes,id',
            "products.*.note"               => 'nullable|string',
            "products.*.price"              => 'required|regex:/^\d+(\.\d{1,2})?$/',
            "products.*.discount"           => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            "products.*.qty"                => 'nullable|numeric|integer',
            "products.*.parent_id"          => 'nullable|numeric|integer',
            'payments'                      => 'nullable|array',
            'payments.*.payment_method_id'  => 'nullable|exists:general_payment_methods,id',
            'payments.*.paid'               => 'nullable',
            'payments.*.exchange'           => 'nullable',
            'payments.*.is_active'          => 'nullable|numeric|in:0,1',


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

    public function validated($key = null, $default = null)
    {
        return array_merge($this->validator->validated(),[
            'order_type'   => "sales",
            'type'         => "customer",
        ]);
    }
}
