<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomTableRequest extends FormRequest
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
            'company_id'            => 'required|integer',
            'table_name'            => 'required|string|max:255',
            'columns'               => 'required|array',
            'columns.*.column_name' => 'required|string|max:255',
            'columns.*.is_required' => 'required|in:0,1|integer',
            'columns.*.is_visible'  => 'required|in:0,1|integer',
        ];
    }
}
