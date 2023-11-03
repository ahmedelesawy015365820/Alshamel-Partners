<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneralItemRequest extends FormRequest
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

        $id = $this->id;

        return [
            'name' => "nullable|string|max:100|" . Rule::unique('general_items', 'name')->whereNull('deleted_at')->ignore($id),
            'name_e' => "nullable|string|max:100|" . Rule::unique('general_items', 'name_e')->whereNull('deleted_at')->ignore($id),
            'price' => 'numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'code_number' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'unit_id' => 'nullable|exists:booking_units,id',
            "company_id" => 'nullable',
        ];
    }
}
