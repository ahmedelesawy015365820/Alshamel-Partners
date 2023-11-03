<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveFileRequest extends FormRequest
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
            "arch_doc_type_id" => "required|exists:arch_doc_types,id",
            "arch_department_id" => "nullable|exists:arch_departments,id",
            "data_type_value" => "required",
            "user_id" => "nullable|exists:users,id",
            "company_id"=>'nullable',
            // "media.*" => ["exists:media,id", new \App\Rules\MediaRule()],
            // 'old_media.*' => ['exists:media,id', new \App\Rules\MediaRule("Modules\Archiving\Entities\DocStatus")],
        ];
    }

}
