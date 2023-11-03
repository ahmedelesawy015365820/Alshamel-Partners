<?php

namespace Modules\RealEstate\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class RlstPolicyRequest extends FormRequest
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
            'name'              => 'required|string|max:50|unique:rlst_policies,name,' . $this->id,
            'name_e'            => 'required|string|max:50|unique:rlst_policies,name_e,' . $this->id,
            'description'       => 'nullable|string|max:255',
        ];
    }


}
