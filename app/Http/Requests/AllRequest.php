<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllRequest extends FormRequest
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
            "parent_id" => "nullable|exists:general_modules,id",
            "is_active" => "nullable|in:active,inactive",
            "is_quotation" => "nullable",
            "sort" => "nullable|integer",
            "page" => "nullable|integer",
            "per_page" => "nullable|integer",
            "is_default" => "nullable|in:0,1",
            "is_employee" => "nullable|in:0,1",
            "columns" => "array"
        ];
    }
}
