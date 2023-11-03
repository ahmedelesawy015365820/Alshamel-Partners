<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
            'arch_doc_type' => ['required', 'integer'],
            'doc_description' => ['required', 'string', 'max:500'],
            'doc_status' => ['required', 'integer'],
            'url_reference' => ['required', "url", "string", "max:200"],
            "company_id"=>'nullable',
        ];
    }


}
