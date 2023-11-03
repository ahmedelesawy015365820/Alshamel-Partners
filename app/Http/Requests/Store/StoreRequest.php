<?php

namespace App\Http\Requests\Store;


use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'nullable|unique:general_stores,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'nullable|unique:general_stores,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'company_id' => 'nullable|integer',
            'branch_id' => 'required|integer',
            'is_active' => 'nullable|in:active,inactive',
        ];
    }

}
