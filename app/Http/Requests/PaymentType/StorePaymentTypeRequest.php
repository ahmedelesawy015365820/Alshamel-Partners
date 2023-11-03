<?php

namespace App\Http\Requests\PaymentType;


use Illuminate\Foundation\Http\FormRequest;

class StorePaymentTypeRequest extends FormRequest
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
            'name' => 'nullable|string|max:255|unique:general_payment_types,name',
            'name_e' => 'nullable|string|max:255|unique:general_payment_types,name_e',
            'is_default' => 'nullable|in:1,0',
        ];
    }

}
