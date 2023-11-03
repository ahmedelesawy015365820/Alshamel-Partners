<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PanelRequest extends FormRequest
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
            'name' => 'nullable|unique:boards_rent_panels,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'nullable|unique:boards_rent_panels,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            "description" => "nullable|string",
            "description_e" => "nullable|string",
            "price" => 'required|array',
            "code" => "required|string",
            "new_code" => "nullable|string",
            "size" => "required|string",
            "note" => "nullable|string",
            "face" => 'required|in:A,B,Multi,One-Face',
            "unit_status_id" => "required|exists:boards_rent_unit_statuses,id",
            "category_id" => "required|exists:general_categories,id",
            // locations
            "country_id" => "nullable|exists:general_countries,id",
            "governorate_id" => "nullable|exists:general_governorates,id",
            "city_id" => "nullable|exists:general_cities,id",
            "avenue_id" => "nullable|exists:general_avenues,id",
            "street_id" => "nullable|exists:general_streets,id",
            "lat" => "required|numeric",
            "lng" => "required|numeric",
            "company_id"=>'nullable',
        ];
    }

}
