<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:general_categories,name,' . ($this->method() == 'PUT' ? $this->id : ''),
            'name_e' => 'required|string|max:255|unique:general_categories,name_e,' . ($this->method() == 'PUT' ? $this->id : ''),
            'price' => 'nullable|numeric',
            "company_id"=>'nullable',
        ];

    }

}
