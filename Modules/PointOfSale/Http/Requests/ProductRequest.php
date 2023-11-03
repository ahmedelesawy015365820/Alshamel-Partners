<?php

namespace Modules\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],

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
