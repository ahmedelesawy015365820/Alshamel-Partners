<?php

namespace App\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
            'name' => 'nullable|unique:general_units,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'nullable|unique:general_units,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'is_active' => 'nullable|in:active,inactive',
            // "module_id" => "required",
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
            'module_id.required' => __('message.field is required'),

        ];
    }
}
