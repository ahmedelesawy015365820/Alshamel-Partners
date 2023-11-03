<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvenueRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:general_avenues,name,' . ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'required|string|max:255|unique:general_avenues,name_e,' . ($this->method() == 'PUT' ?  $this->id : ''),
            "is_active" => "nullable|in:active,inactive",
            "country_id" => "nullable|exists:general_countries,id",
            "city_id" => "nullable|exists:general_cities,id",
            "governorate_id" => "nullable|exists:general_governorates,id",
        ];
    }

}
