<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
                Rule::unique('general_brands')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            'name_e' => [
                'required',
                Rule::unique('general_brands')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],

            'company_id' => 'nullable',

        ];
    }
}
