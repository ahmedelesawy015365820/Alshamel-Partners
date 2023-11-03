<?php

namespace Modules\Custody\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class TypeRequest extends FormRequest
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
            'name' => "required|string|unique:cus_types,name,{$this->id},id",
            'name_e' => "required|string|unique:cus_types,name_e,{$this->id},id",
            "company_id"=>'nullable',
        ];
    }

}
