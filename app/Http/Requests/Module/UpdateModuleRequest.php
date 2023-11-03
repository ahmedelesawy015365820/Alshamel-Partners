<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
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
        $id = $this->id;
        return [
            'name' => 'required|string|max:255|unique:modules,name,' . $id,
            'name_e' => 'required|string|max:255|unique:modules,name_e,' . $id,
            'parent_id' => ["nullable", new \App\Rules\NotInChildrenRule(), "exists:modules,id", "not_in:" . $id],
            "is_active" => "nullable|in:active,inactive",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('message.field is required'),
            'name.string' => __('message.field must be string'),
            'name.max' => __('message.field must be less than 255 character'),
            'name.unique' => __('message.field must be unique'),
            'name_e.required' => __('message.field is required'),
            'name_e.string' => __('message.field must be string'),
            'name_e.max' => __('message.field must be less than 255 character'),
            'name_e.unique' => __('message.field must be unique'),
            'parent_id.exists' => __('message.field must be exists'),
            'parent_id.not_in' => __('message.field must be not in children'),
            'parent_id.not_in_children' => __('message.field must be not in children'),

        ];
    }
}
