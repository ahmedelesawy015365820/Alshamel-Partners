<?php

namespace App\Http\Requests\CustomerCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerCategoryRequest extends FormRequest
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
        $request =  request();

        return [
            'name' => [
                'required',
                Rule::unique('general_customer_categories')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            'name_e' => [
                'required',
                Rule::unique('general_customer_categories')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            "parent_id" => 'nullable|exists:general_customer_categories,id',
            'company_id' => 'nullable|integer',
        ];
    }
}
