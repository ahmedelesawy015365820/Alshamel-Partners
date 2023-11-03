<?php

namespace Modules\BoardsRent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255|unique:boards_rent_packages,name' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'sometimes|string|max:255|unique:boards_rent_packages,name_e' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'code' => 'sometimes|string|max:255|unique:boards_rent_packages,code' .
            ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'price' => 'sometimes|regex:/^\d+(\.\d{1,2})?$/',
            "panels" => "sometimes|array",
            "panels.*" => "required_with:panels|exists:boards_rent_panels,id",

            "category_id"    => "nullable|exists:general_categories,id",
            "governorate_id" => "nullable|exists:general_governorates,id",
            "company_id"     => 'nullable',
            "note"           => 'nullable|string',
        ];

    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
