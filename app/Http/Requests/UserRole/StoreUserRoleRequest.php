<?php

namespace App\Http\Requests\UserRole;


use Illuminate\Foundation\Http\FormRequest;

class StoreUserRoleRequest extends FormRequest
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
            "role" => "nullable|exists:general_roles,id",
            "users" => "nullable|array",
            "users.*" => "nullable|",
            // "users.*" => "required|exists:general_users,id|unique:general_role_user,user_id",
            "company_id"=>"nullable"
        ];
    }

}
