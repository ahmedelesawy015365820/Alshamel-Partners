<?php

namespace App\Http\Requests\ExternalSalesmen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExternalSalesmenRequest extends FormRequest
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

    public function rules()
    {
        $id = $this->id;
        return [
            "phone" => "nullable|max:20|unique:general_external_salesmen,phone," . $id,
            "address" => "nullable|max:255",
            "rp_code" => "nullable|unique:general_external_salesmen,rp_code," . $id,
            "email" => "nullable|email|unique:general_external_salesmen,email," .$id,
            "country_id" => "nullable|exists:general_countries,id",
            'national_id' => "nullable|integer",
            'is_active' => 'nullable|in:active,inactive',
        ];
    }


}
