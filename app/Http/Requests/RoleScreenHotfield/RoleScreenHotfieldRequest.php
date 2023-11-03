<?php

namespace App\Http\Requests\RoleScreenHotfield;

use Illuminate\Foundation\Http\FormRequest;

class RoleScreenHotfieldRequest extends FormRequest
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
            "role_id"       => "nullable|exists:roles,id" ,
            "screen_id"     => "nullable|numeric" ,
            "hotfield_id"   => "nullable|numeric"
        ];
    }
}
