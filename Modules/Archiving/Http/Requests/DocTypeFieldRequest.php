<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocTypeFieldRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'doc_type_id' => ['required'],
            'doc_field_id' => ['required'],
            // 'field_characters' => ['nullable', 'string'],
            'field_order' => ['required', 'integer'],
            'is_required' => ['required', Rule::in(['1', '0'])],
            "company_id"=>'nullable',
        ];
    }


}
