<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralAccountsRequest extends FormRequest
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
            'name' => 'required|string|max:64|unique:general_accounts,name,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'name_e' => 'required|string|max:64|unique:general_accounts,name_e,' . ($this->method() == 'PUT' ?  $this->id : '') ,
            'account_number' => 'required|string|max:20|unique:general_accounts,account_number,' . ($this->method() == 'PUT' ?  $this->id : '') ,
        ];
    }
}
