<?php

namespace Modules\Custody\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    //
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
        if ($this->method() == "PUT") {
            return [
                'name' => "required|string|max:255",
                'name_e' => "required|string|max:255",
                'model_type' => 'required|string|in:' . get_class(new \App\Models\User()) . ',' . get_class(new \App\Models\Employee()),
                'model_id' => 'required|integer',
                'status' => 'required|in:limited,unlimited',
                'default_amount' => 'required|numeric',
                'types' => 'required|array',
                'types.*' => 'required|integer',
                "company_id"=>'nullable',
                // "last_update_user_id" => "required|exists:general_users,id",

            ];
        }
        // laravel validation exists in users or in employees for model_id

        return [
            'name' => "required|string|max:255",
            'name_e' => "required|string|max:255",
            'model_type' => 'required|string|in:' . get_class(new \App\Models\User()) . ',' . get_class(new \App\Models\Employee()),
            'model_id' => 'required|integer',
            'status' => 'required|in:limited,unlimited',
            'default_amount' => 'required|numeric',
            'types' => 'required|array',
            'types.*' => 'required|integer',
            "company_id"=>'nullable',
            // "creation_user_id" => "required|exists:general_users,id",
        ];
    }

}
