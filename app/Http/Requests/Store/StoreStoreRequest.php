<?php

namespace App\Http\Requests\Store;


use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'name_e' => 'nullable|string|max:255',
            'company_id' => 'nullable|integer',
            'branch_id' => 'nullable|integer',
            'is_active' => 'nullable|in:active,inactive',
        ];
    }

}
