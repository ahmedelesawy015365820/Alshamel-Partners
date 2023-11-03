<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocStatusRequest extends FormRequest
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
                'name' => ['required', 'string', 'max:255',"unique:arch_doc_statuses,name," . $this->id],
                'name_e' => ['required', 'string', 'max:255',"unique:arch_doc_statuses,name_e," . $this->id],
                "company_id"=>'nullable',
            ];
        }
        return [
            'name' => ['required', 'string', 'max:255',"unique:arch_doc_statuses,name"],
            'name_e' => ['required', 'string', 'max:255',"unique:arch_doc_statuses,name_e"],
            "company_id"=>'nullable',
        ];
    }


}
