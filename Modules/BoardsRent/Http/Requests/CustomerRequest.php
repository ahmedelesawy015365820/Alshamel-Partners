<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'name_e' => 'nullable|string|max:255',
            "phone" => "nullable|string|max:255|unique:general_customers,phone" .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            "email" => "nullable|string|max:255|email|unique:general_customers,email" .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            "country_id" => "nullable|exists:general_countries,id",
            "city_id" => "nullable|exists:general_cities,id",
            "avenue_id" => "nullable|exists:general_avenues,id",
            "facebook" => "nullable|string|max:255",
            "instagram" => "nullable|string|max:255",
            "linkedin" => "nullable|string|max:255",
            "twitter" => "nullable|string|max:255",
            "snapchat" => "nullable|string|max:255",
            'website' => "nullable|string|max:255|active_url",
            "salesman_id" => "nullable|exists:general_salesmen,id",
            "customer_source_id" => "nullable|exists:boards_rent_customer_sources,id",
            "sector_id" => "nullable|exists:boards_rent_sectors,id",
        ];
    }

}
