<?php

namespace App\Http\Requests\ExternalSalesmen;

use Illuminate\Foundation\Http\FormRequest;

class ExternalSalesmenRequest extends FormRequest
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
            "phone" => "nullable|max:20|required|unique:general_external_salesmen,phone,". ($this->method() == 'PUT' ?  $this->id : ''),
            "address" => "nullable|max:255",
            "rp_code" => "nullable|unique:general_external_salesmen,rp_code,". ($this->method() == 'PUT' ?  $this->id : ''),
            "email" => "nullable|email|unique:general_external_salesmen,email,". ($this->method() == 'PUT' ?  $this->id : ''),
            "country_id" => "nullable|exists:general_countries,id",
            'national_id' => "nullable|integer",
            'is_active' => 'nullable|in:active,inactive',
            'name' => 'nullable|string|max:255|unique:general_external_salesmen,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'nullable|string|max:255|unique:general_external_salesmen,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
        ];
    }


}
