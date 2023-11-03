<?php

namespace Modules\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'nullable',
            'paid' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'exchange' => 'nullable', 'numeric', 'regex:/^\d{1,18}(\.\d{1,2})?$/',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'options' => 'nullable|string|max:255',
            'order_id' => 'nullable|exists:pos_orders,id',
            'cash_register_id' => 'nullable',
            'is_active' => 'nullable|in:1,0',
        ];

    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
