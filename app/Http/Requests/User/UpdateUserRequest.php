<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "media" => "nullable|array",
            "media.*" => ["exists:media,id", new \App\Rules\MediaRule()],
            'old_media.*' => ['exists:media,id', new \App\Rules\MediaRule("App\Models\User")],
            'email' => 'nullable|string|email|max:255|unique:general_users,id,' . $this->id,
            'password' => 'nullable|string|min:8',
            'employee_id' => 'nullable|exists:general_employees,id',
            'role_id' => 'nullable|exists:roles,id'
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
            'name.string' => __('message.field must be string'),
            'name.max' => __('message.field must be less than 255 character'),
            'name_e.string' => __('message.field must be string'),
            'name_e.max' => __('message.field must be less than 255 character'),
            'is_active.in' => __('message.field must be in active,inactive'),
            'media.exists' => __('message.field must be exists in media table'),
            'email.string' => __('message.field must be string'),
            'email.email' => __('message.field must be email'),
            'email.max' => __('message.field must be less than 255 character'),
            'email.unique' => __('message.field must be unique'),
            'password.string' => __('message.field must be string'),
            'password.min' => __('message.field must be more than 8 character'),
            'password.confirmed' => __('message.field must be confirmed'),
            'employee_id.exists' => __('message.field must be exists'),
            'media.exists' => __('message.field must be exists'),
            'media.media' => __('message.field must be media'),

        ];
    }
}
