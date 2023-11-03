<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
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
        if ($this->method() == 'PUT'):
            return [

                "bank_id" => "nullable|exists:general_banks,id",
                "account_number" => "nullable|string|max:255",
                "phone" => "nullable|string|max:255",
                "address" => "nullable|string|max:255",
                "email" => "nullable|string|max:255",
                "emp_id" => "nullable|exists:general_employees,id",
                "rp_code" => "nullable|string|max:255",
                "media" => "nullable|array",
                "media.*" => ["nullable",'exists:media,id', new \App\Rules\MediaRule()],
                'old_media.*' => ['nullable','exists:media,id', new \App\Rules\MediaRule("App\Models\BankAccount")],
            ];
        else:
            return [
                "bank_id" => "nullable|exists:general_banks,id",
                "account_number" => "nullable|string|max:255",
                "phone" => "nullable|string|max:255",
                "address" => "nullable|string|max:255",
                "email" => "nullable|string|max:255",
                "emp_id" => "nullable|exists:general_employees,id",
                "rp_code" => "nullable|string|max:255",
                "media" => "nullable|array",
                "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],

            ];
        endif;


    }


}
