<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentFieldRequest extends FormRequest
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
        // if update action with method put will be unique expect id
        if ($this->method() == 'PUT') {
            return [
                'name' => ['required', 'string', 'max:255', "unique:arch_document_fields,name," . $this->id],
                'name_e' => ['required', 'string', 'max:255', "unique:arch_document_fields,name_e," . $this->id],
                // 'type' => ['required'],
//                "tree_property_id" => ["required", "exists:general_tree_properties,id"],
                'is_reference' => ['required', Rule::in(['1', '0'])],
                "field_characters" => "nullable|string",
                "company_id"=>'nullable',
            ];
        }
        return [
            'name' => ['required', 'string', 'max:255', "unique:arch_document_fields,name"],
            'name_e' => ['required', 'string', 'max:255', "unique:arch_document_fields,name_e"],
            // 'type' => ['required'],
            // "tree_property_id" => ["required", "exists:general_tree_properties,id"],
            'is_reference' => ['required', Rule::in(['1', '0'])],
            "company_id"=>'nullable',
        ];
    }



}
