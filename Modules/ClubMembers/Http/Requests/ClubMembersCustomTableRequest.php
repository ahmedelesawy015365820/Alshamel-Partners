<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubMembersCustomTableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id'            => 'nullable|integer',
            'table_name'            => 'required|string|max:255',
            'columns'               => 'required|array',
            'columns.*.column_name' => 'required|string|max:255',
            'columns.*.is_required' => 'required|in:0,1|integer',
            'columns.*.is_visible'  => 'required|in:0,1|integer',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
