<?php

namespace App\Http\Requests\DocumentStatuse;

use Illuminate\Foundation\Http\FormRequest;

class DocumentStatuseRequest extends FormRequest
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
            'name' => 'nullable|unique:general_document_statuses,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'nullable|unique:general_document_statuses,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'is_active' => 'nullable|in:0,1',
            "is_default" => "nullable|in:0,1",
            "company_id"=>'nullable',
        ];
    }
}
