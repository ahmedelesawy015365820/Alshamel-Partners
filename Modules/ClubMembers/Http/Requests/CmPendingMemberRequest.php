<?php

namespace Modules\ClubMembers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CmPendingMemberRequest extends FormRequest
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
            'name'      => 'sometimes|string|max:255|unique:cm_pending_members,name' . ($this->method() == 'PUT' ? ',' . $this->id : ''),
            'name_e'    => 'sometimes|string|max:255|unique:cm_pending_members,name_e' . ($this->method() == 'PUT' ? ','. $this->id : ''),
            'status'    => 'sometimes|string|max:255|unique:cm_pending_members,status' . ($this->method() == 'PUT' ? ','. $this->id : '') ,
            "company_id"=>'nullable',
        ];
    }



}
