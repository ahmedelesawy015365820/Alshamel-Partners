<?php

namespace App\Http\Requests\Sector;

use Illuminate\Foundation\Http\FormRequest;

class SectorRequest extends FormRequest
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
            'name' => 'required|unique:general_sectors,name,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'name_e' => 'required|unique:general_sectors,name_e,'. ($this->method() == 'PUT' ?  $this->id : ''),
            'parent_id' => 'nullable|exists:general_sectors,id'
        ];
    }
}
