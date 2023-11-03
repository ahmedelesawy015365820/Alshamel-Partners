<?php

namespace App\Http\Requests\PaymentType;


use Illuminate\Foundation\Http\FormRequest;

class PaymentTypeRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:general_payment_types,name'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e' => 'sometimes|string|max:255|unique:general_payment_types,name_e'. ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'is_default' => 'nullable|in:1,0',
        ];
    }

}
