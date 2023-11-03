<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => 'required|string',
            'name_e' => "required|string",
            'description' => 'nullable|string',
            'description_e' => 'nullable|string',
            'socials' => 'nullable|array',
            'phones' => 'nullable|array',
            'job_id' => 'required|integer|exists:general_screen_tree_properties,id',
            'priority_id' => 'required|integer|exists:general_screen_tree_properties,id',
            "company_id"=>'nullable',
        ];
    }
}
