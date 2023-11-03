<?php

namespace Modules\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReturnPurchasesProductsRequest extends FormRequest
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
            'supplier_id'                   => 'nullable|exists:general_suppliers,id',
            'branch_id'                     => 'required|exists:general_branches,id',
            'document_id'                   => 'required|exists:general_documents,id',
            'order_id'                      => 'required|exists:pos_orders,id',
            'returned_invoice'              => 'required|string',
            'order_return_type'             => 'required|in:fully,partial',
            'created_by'                    => 'required|string',
            'products.*.product_id'         => 'required|exists:pos_products,id',
            'products.*.product_variant_id' => 'required|exists:pos_product_variants,id',
            'products.*.tax_id'             => 'nullable|exists:general_taxes,id',
            "products.*.note"               => 'nullable|string',
            "products.*.price"              => 'required|regex:/^\d+(\.\d{1,2})?$/',
            "products.*.discount"           => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            "products.*.qty"                => 'required|integer',
            "products.*.parent_id"          => 'required||exists:pos_order_items,id',
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
            'order_type' => 'receiving',
            'type'       => 'supplier',
            'date'       => now()->format('Y-m-d'),
            'sub_total'  => 0,
            'total_tax'  => 0,
            'profit'     => 0,
        ]);
    }

}
