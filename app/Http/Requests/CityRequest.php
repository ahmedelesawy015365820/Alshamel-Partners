<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
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
            "name" => "nullable|unique:general_cities,name,". ($this->method() == 'PUT' ?  $this->city : ''),
            "name_e" => "nullable|unique:general_cities,name_e,". ($this->method() == 'PUT' ?  $this->city : ''),
            "country_id" => "nullable|exists:general_countries,id",
            "governorate_id" => "nullable|exists:general_governorates,id",
            "company_id" => "nullable",
            "is_active"=>"nullable"
        ];
    }
}
