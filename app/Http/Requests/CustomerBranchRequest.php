<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerBranchRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'name_e' => 'required|string|max:255',
            'customer_id' => 'required|integer|exists:general_customers,id',
            "country_id" => "required|integer|exists:general_countries,id",
            'city_id' => 'required|integer|exists:general_cities,id',
            'governorate_id' => 'required|integer|exists:general_governorates,id',
            'avenue_id' => 'required|integer|exists:general_avenues,id',
            'street_id' => 'required|integer|exists:general_streets,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
