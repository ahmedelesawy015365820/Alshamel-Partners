<?php

namespace App\Http\Requests\Salesman;

use Illuminate\Foundation\Http\FormRequest;

class SalesmanRequest extends FormRequest
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
            'name' => 'nullable|unique:general_salesmen,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'nullable|unique:general_salesmen,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            // "salesman_type_id" => "required|exists:salesmen_types,id",
            "salesman_type_id" => "required|exists:general_salesmen_types,id",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */

    public function messages()
    {
        return [
            "name.required" => __("message.field is required"),
            "name.string" => __("message.field must be string"),
            "name.max" => __("message.field must be less than 100 characters"),
            "name_e.required" => __("message.field is required"),
            "name_e.string" => __("message.field must be string"),
            "name_e.max" => __("message.field must be less than 100 characters"),
            "salesman_type_id.required" => __("message.field is required"),
            "salesman_type_id.exists" => __("message.field must be exists"),

        ];
    }
}
