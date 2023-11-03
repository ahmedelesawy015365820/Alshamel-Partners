<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'name'      => 'sometimes|string|max:255|unique:rlst_items,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e'    => 'sometimes|string|max:255|unique:rlst_items,name_e' . ($this->method() == 'PUT' ? ','. $this->id : '') ,
            'code_number' => 'sometimes|string|max:255',
            'type'=> 'nullable|string',
            'price'=> 'numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'unit_id'=>'nullable|exists:general_units,id,deleted_at,NULL',
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            // "category-item" => 'required|array',
            'category_item_id.*' => 'nullable|exists:rlst_category_item,id,deleted_at,NULL',

            "company_id" => 'nullable',
            // 'category-item.*.item_id'          => 'required|exists:rlst_items,id',
        ];
    }
}
