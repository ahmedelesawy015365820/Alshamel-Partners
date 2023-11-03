<?php

namespace App\Http\Requests\Store;


use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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
            'name' => 'string|max:255',
            'name_e' => 'string|max:255',
            'company_id' => 'integer',
            'branch_id' => 'integer',
            'is_active' => 'nullable|in:active,inactive',

        ];
    }

}
