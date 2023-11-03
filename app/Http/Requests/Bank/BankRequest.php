<?php

namespace App\Http\Requests\Bank;

use App\Rules\BankUniqueNameCountryRule;
use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
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
        if ($this->method() == 'PUT'):
            return [
                'name' => ['nullable', 'max:255', 'string', new BankUniqueNameCountryRule($this->country_id, $this->id)],
                'name_e' => ['nullable', 'max:255', 'string', new BankUniqueNameCountryRule($this->country_id, $this->id)],
                "country_id" => "nullable|exists:general_countries,id",
                "swift_code" => "nullable|string",
            ];
        else:
            return [
                'name' => ['required', 'max:255', 'string', new BankUniqueNameCountryRule($this->country_id)],
                'name_e' => ['required', 'max:255', 'string', new BankUniqueNameCountryRule($this->country_id)],
                "country_id" => "required|exists:general_countries,id",
                "swift_code" => "required|string",
            ];
        endif;

    }

}
