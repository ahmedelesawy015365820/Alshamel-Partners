<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
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
                'id' => 'nullable',
                "name" => "nullable|unique:general_branches,name," . $this->branch,
                "name_e" => "nullable|unique:general_branches,name_e," . $this->branch,
                "country_id" => "nullable|exists:general_countries,id",
                "is_active" => "nullable",
                "parent_id" => "nullable",
                'code_country' => "nullable",
                'phone' => "nullable",
                'second_phone' => "nullable",
                'fax' => "nullable",
                'p_o_pox' => "nullable",
                'address' => "nullable|string|max:255",
                'email' => "nullable|email",
                "media" => "nullable|array",

                "media.*" => ["nullable", 'exists:media,id', new \App\Rules\MediaRule()],
                'old_media.*' => ['nullable', 'exists:media,id'],
            ];
        else:
            return [
                'id' => 'nullable',
                "name" => "nullable|unique:general_branches,name",
                "name_e" => "nullable|unique:general_branches,name_e",
                "country_id" => "nullable|exists:general_countries,id",
                "is_active" => "nullable",
                "parent_id" => "nullable",
                'code_country' => "nullable",
                'phone' => "nullable",
                'second_phone' => "nullable",
                'fax' => "nullable",
                'p_o_pox' => "nullable",
                'address' => "nullable|string|max:255",
                'email' => "nullable|email",
                "media" => "nullable|array",
                "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
                'old_media.*' => ['nullable', 'exists:media,id', new \App\Rules\MediaRule("App\Models\Branch")],

            ];
        endif;
    }

}
