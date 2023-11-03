<?php

namespace App\Http\Requests\CustomerGroup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerGroupRequest extends FormRequest
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
            'title' => [
                 'required',
                 Rule::unique('general_customer_groups')->ignore($this->id)->where(function ($query) use($request) {
                     return $query->where('company_id', $request->company_id);
                 })->whereNull("deleted_at"),
            ],
            'title_e' => [
                'required',
                Rule::unique('general_customer_groups')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('company_id', $request->company_id);
                })->whereNull("deleted_at"),
            ],
            "discount" =>'required' ,
            "is_default" => 'nullable|in:1,0',
            'company_id' => 'nullable|integer',
        ];
    }
}
