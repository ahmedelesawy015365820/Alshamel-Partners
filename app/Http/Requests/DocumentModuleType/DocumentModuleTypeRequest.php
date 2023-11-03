<?php

namespace App\Http\Requests\DocumentModuleType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentModuleTypeRequest extends FormRequest
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
        $request =  request();

        return [

            'name' => [
                'sometimes',
                Rule::unique('general_document_module_types')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('db_table', $request->db_table);
                }),
            ],

            'name_e' => [
                'sometimes',
                Rule::unique('general_document_module_types')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('db_table', $request->db_table);
                }),
            ],

            'title' => [
                'sometimes',
                Rule::unique('general_document_module_types')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('db_table', $request->db_table);
                }),
            ],


            'title_e' => [
                'sometimes',
                Rule::unique('general_document_module_types')->ignore($this->id)->where(function ($query) use($request) {
                    return $query->where('db_table', $request->db_table);
                }),
            ],

            'db_table' => 'nullable|string|max:255',



        ];
    }
}
