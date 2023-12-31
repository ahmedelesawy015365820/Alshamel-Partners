<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
            'content' => 'required|string|max:500',
            'content_e' => 'required|string|max:500',
            'message_type_id' => 'nullable|integer|exists:general_message_types,id|unique:general_messages,message_type_id,' . ($this->method() == 'PUT' ? $this->id : ''),
            'module' => 'nullable|string|max:255',
        ];
    }
}
