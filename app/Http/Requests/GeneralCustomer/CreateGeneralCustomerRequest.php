<?php

namespace App\Http\Requests\GeneralCustomer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateGeneralCustomerRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [],
            'name_e' => [],
            'phone' => [],
            'email' => [],
            'country_id' => ['nullable'],
            'city_id' => ['nullable'],
            'rp_code' => ['nullable'],
            'nationality' => [],
            'bank_account_id' => ['nullable'],
            'contact_phone' => [],
            'national_id' => [],
            'whatsapp' => [],
            'passport_no' => [],
            'contact_person' => [],
            'note1' => [],
            'note2' => [],
            'note3' => [],
            'note4' => [],
            'company_id' => [],
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
        ];
    }

}
