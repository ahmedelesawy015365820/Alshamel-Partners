<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreatePropertyRequest extends FormRequest
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
            '*.name' => 'required|string',
            '*.name_e' => 'required|string',
            '*.parent_id' => 'nullable|integer|exists:general_tree_id',
            '*.required' => 'nullable|in:0,1',
        ];
    }
}
