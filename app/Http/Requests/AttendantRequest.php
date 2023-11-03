<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendantRequest extends FormRequest
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
            "national_id" => 'nullable|string|max:20',
            "id_number" => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            "customer_id" => "nullable|exists:general_customers,id",

        ];
    }
}
