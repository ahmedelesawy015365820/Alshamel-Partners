<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepertmentRequest extends FormRequest
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
            'id' => 'nullable',
            'name' => ['nullable', 'string', 'max:255', Rule::unique('general_departments')->ignore($this->id)],
            'name_e' => ['nullable', 'string', 'max:255', Rule::unique('general_departments')->ignore($this->id)],
            //'supervisors' => ['nullable', 'array'],
            //'attentions' => ['nullable', 'array'],
            //'supervisors.*'
            //=> ['required_with:supervisors', 'exists:general_employees,id'],
            //'attentions.*'
            //=> ['required_with:attentions', 'exists:general_employees,id'],
            "company_id"=>'nullable',

        ];
    }
}
