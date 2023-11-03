<?php

namespace App\Http\Requests\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|unique:general_payment_methods,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'nullable|string|max:255|unique:general_payment_methods,name_e' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'is_default' => 'nullable|in:1,0',
            "company_id" => 'nullable',
            'type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'created_by' => 'nullable|string|max:255',
        ];
    }

}
