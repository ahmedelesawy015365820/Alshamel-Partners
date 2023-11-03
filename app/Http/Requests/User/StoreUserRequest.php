<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'name_e' => 'nullable|string|max:255',
            'is_active' => 'nullable|in:active,inactive',

            // "media" => ["required", "exists:media,id", new \App\Rules\MediaRule()],
            'email' => 'nullable|string|email|max:255|unique:general_users,email',
            'password' => 'nullable|string|min:0',
            'employee_id' => 'nullable|exists:general_employees,id',
            'role_id' => 'nullable|exists:roles,id',
            'company_id' => 'nullable'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */

    public function messages()
    {
        return [
            'name.required' => __('message.field is required'),
            'name.string' => __('message.field must be string'),
            'name.max' => __('message.field must be less than 255 character'),
            'name_e.required' => __('message.field is required'),
            'name_e.string' => __('message.field must be string'),
            'name_e.max' => __('message.field must be less than 255 character'),
            'is_active.in' => __('message.field must be in active,inactive'),
            'media.required' => __('message.field is required'),
            'media.exists' => __('message.field must be exists in media table'),
            'email.required' => __('message.field is required'),
            'email.string' => __('message.field must be string'),
            'email.email' => __('message.field must be email'),
            'email.max' => __('message.field must be less than 255 character'),
            'email.unique' => __('message.field must be unique'),
            'password.required' => __('message.field is required'),
            'password.string' => __('message.field must be string'),
            'password.min' => __('message.field must be more than 8 character'),
            'password.confirmed' => __('message.field must be confirmed'),
            'employee_id.exists' => __('message.field must be exists'),
            'media.media' => __('message.field must be media'),

        ];
    }
}
