<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocTypeRequest extends FormRequest
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
                'name' => ['required', 'string', 'max:255',"unique:arch_doc_types,name," . $this->id],
                'name_e' => ['required', 'string', 'max:255',"unique:arch_doc_types,name_e," . $this->id],
                'is_valid' => ['required', Rule::in(['1', '0'])],
                "company_id"=>'nullable',

            ];
        }
        return [
            'name' => ['required', 'string', 'max:255',Rule::unique("arch_doc_types")->whereNull("deleted_at")],
            'name_e' => ['required', 'string', 'max:255',Rule::unique("arch_doc_types")->whereNull("deleted_at")],
            'is_valid' => ['required', Rule::in(['1', '0'])],
            "company_id"=>'nullable',

        ];
    }



}
