<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmMemberAcceptRequest extends FormRequest
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
        return [
            'acceptance' => 'nullable',
            'session_date'=>'required|date',
            'session_number'=>'required|string|max:255',
            'membership_date' =>'required|date',
            'membership_number' =>  'nullable|string|max:255',

        ];
    }
}
