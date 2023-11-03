<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralMainCostCentersRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:64|unique:general_main_cost_centers,name,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'name_e' => 'required|string|max:64|unique:general_main_cost_centers,name_e,' . ($this->method() == 'PUT' ?  $this->id : '') ,
        ];
    }
}
