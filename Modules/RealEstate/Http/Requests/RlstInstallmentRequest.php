<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstInstallmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            "pay_type" => "required|in:down_payment,quarter_per,half_per,year_per,installment",
            'amount' => 'required|numeric',
            "currency_id" => "required|integer|exists:general_currencies,id",
            'rest_amount' => 'required|numeric',
            "company_id"=>'nullable',

        ];
    }


}
