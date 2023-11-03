<?php

namespace Modules\Archiving\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
                "name" => ["required", "string", "max:100", "unique:arch_departments,name," . $this->id],
                "name_e" => ["required", "string", "max:100"],
                "parent_id" => ['nullable', 'integer'],
                "is_active" => "nullable|in:active,inactive",
                "is_key"=>[],
                "key_value"=>[],
                "company_id"=>'nullable',
            ];
        }
        return [
            "name" => ["required", "string", "max:100", "unique:arch_departments,name"],
            "name_e" => ["required", "string", "max:100", "unique:arch_departments,name_e"],
            "parent_id" => ['nullable', 'integer'],
            "is_active" => "nullable|in:active,inactive",
            "is_key"=>[],
            "key_value"=>[],
            "company_id"=>'nullable',
        ];
    }



}
