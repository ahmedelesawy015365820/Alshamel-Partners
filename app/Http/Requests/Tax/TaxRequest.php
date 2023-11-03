<?php

namespace App\Http\Requests\Tax;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxRequest extends FormRequest
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
                Rule::unique('general_taxes')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            'name_e' => [
                'required',
                Rule::unique('general_taxes')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                }),
            ],
            "percentage" =>'required' ,
            "is_default" => 'nullable|in:1,0',
            'company_id' => 'nullable|integer',
        ];
    }
}
