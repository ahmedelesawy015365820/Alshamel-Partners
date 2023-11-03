<?php

namespace App\Http\Requests\RoleWorkflow;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleWorkflowRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_id' => ['required', 'exists:general_roles,id'],
            'workflow_id' => ['required'],
            'workflow_name' => ['required'],
            "company_id"=>["required"]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
