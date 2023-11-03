<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryItemRequest extends FormRequest
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
            'name'      => 'sometimes|string|max:255|unique:rlst_category_item,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e'    => 'sometimes|string|max:255|unique:rlst_category_item,name_e' . ($this->method() == 'PUT' ? ','. $this->id : '') ,
            'parent_id' => ["nullable"],
            "company_id"=>'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => __('message.name already exists'),
            'name_e.unique' => __('message.name already exists'),
            'parent_id.exists' => __('message.parent not found'),
        ];
    }



}
