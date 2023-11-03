<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name' => 'nullable|string|max:255,',
            'name_e' => 'nullable|string|max:255',
            "is_default" => "nullable|in:0,1",
            "phone_key" => "nullable|unique:general_countries,phone_key," . ($this->method() == 'PUT' ?  $this->id : ''),
            'national_id_length' => "nullable|integer",
            'long_name' => "nullable|max:100",
            'long_name_e' => "nullable|max:100",
            'short_code' => "nullable|max:10",
            'is_active' => 'nullable|in:active,inactive',
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
        ];
    }
}
