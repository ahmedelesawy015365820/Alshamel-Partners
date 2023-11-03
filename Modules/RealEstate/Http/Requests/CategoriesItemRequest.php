<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesItemRequest extends FormRequest
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
            'category_item_id' => 'sometimes|exists:rlst_category_item,id,deleted_at,NULL',
            'item_id' => 'sometimes|exists:rlst_items,id,deleted_at,NULL',
            "company_id"=>'nullable',
        ];
    }

}
