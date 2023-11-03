<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellMethodRequest extends FormRequest
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
            'name' => 'sometimes|unique:boards_rent_sell_methods,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'sometimes|unique:boards_rent_sell_methods,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            "commission_ratio" => "sometimes|numeric|min:0|max:100",
            "target_calculation_ratio" => "sometimes|numeric|min:0|max:100",
            "is_all_value" => "nullable|in:1,0",
            "is_default" => "nullable|in:1,0",
            "company_id"=>'nullable',
        ];

    }

}
