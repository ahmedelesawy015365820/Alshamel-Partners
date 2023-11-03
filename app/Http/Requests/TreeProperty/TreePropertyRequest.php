<?php

namespace App\Http\Requests\TreeProperty;

use Illuminate\Foundation\Http\FormRequest;

class TreePropertyRequest extends FormRequest
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
            'name' => 'nullable|unique:general_tree_properties,name,'. ($this->method() == 'PUT' ?  $this->tree_property : ''),
            'name_e' => 'nullable|unique:general_tree_properties,name_e,'. ($this->method() == 'PUT' ?  $this->tree_property : ''),
            'parent_id' => ["nullable",
                // , new \App\Rules\TRNotInChildrenRule()
                // , "exists:tree_properties,id", "not_in:" . $id
            ],
        ];
    }

}
