<?php

namespace Modules\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'title_e' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'description_e' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:general_categories,id',
            'brand_id' => 'nullable|exists:general_brands,id',
            'unit_id' => 'nullable|exists:general_units,id',
            'group_id' => 'nullable|exists:general_groups,id',
            'taxable' => 'nullable|numeric',
            'tax_type' => 'nullable|string|max:255',
            'tax_id' => 'nullable',
            'product_type' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:general_branches,id',
            'is_quantity' => 'nullable|in:0,1',
            'created_by' => 'nullable',

            'sku' => 'nullable|string|unique:pos_product_variants,sku'.','.request()->product_standard_id,
            'product_id' => 'nullable|exists:pos_products,id',
            'variant_title' => 'nullable|string|max:255',
            'attribute_values' => 'nullable|string|max:255',
            'variant_details' => 'nullable|string|max:255',
            'purchase_price' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'selling_price' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'enabled' => 'nullable|in:0,1',
            'isNotify' => 'nullable|string|max:255',
            'bar_code' => 'nullable|string|max:255',
            're_order' => 'nullable|integer|max:255',

            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],

            'product_variant' => 'nullable|array',
            'product_variant.*.product_id' => 'nullable|exists:pos_products,id',
            'product_variant.*.sku' => 'nullable|string|unique:pos_product_variants,sku' .  ',product_variant.*.id',
            'product_variant.*.variant_title' => 'nullable|string|max:255',
            'product_variant.*.attribute_values' => 'nullable|string|max:255',
            'product_variant.*.variant_details' => 'nullable|string|max:255',
            'product_variant.*.purchase_price' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'product_variant.*.selling_price' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'product_variant.*.enabled' => 'nullable|in:0,1',
            'product_variant.*.isNotify' => 'nullable|string|max:255',
            'product_variant.*.bar_code' => 'nullable|string|max:255',
            'product_variant.*.re_order' => 'nullable|integer|max:255',
            'product_variant.*.media' => 'nullable|array',
            'product_variant.*.media.*' => ['nullable',  new \App\Rules\MediaRule()],
            'product_variant.*.product_attributes' => 'nullable|array',
            'product_variant.*.product_attributes.*.product_id' => 'nullable|exists:pos_products,id',
            'product_variant.*.product_attributes.*.attribute_id' => 'nullable|exists:general_attributes,id',
            'product_variant.*.product_attributes.*.values' => 'nullable|string|max:255',


            'payments' => 'nullable|array',
            'payments.*.payment_method_id' => 'nullable|exists:general_payment_methods,id',
            'payments.*.paid' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'payments.*.exchange' => 'nullable|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'payments.*.options' => 'nullable|string|max:255',
            'payments.*.cash_register_id' => 'nullable',

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
