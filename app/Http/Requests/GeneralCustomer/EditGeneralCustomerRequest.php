<?php

namespace App\Http\Requests\GeneralCustomer;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditGeneralCustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>[],
            'name_e'=>[],
            'phone'=>[],
            'email'=>[],
            'country_id'=>['nullable'],
            'city_id'=>['nullable'],
            'rp_code'=>['nullable'],
            'nationality'=>[],
            'bank_account_id'=>['nullable'],
            'contact_phone'=>[],
            'national_id'=>[],
            'whatsapp'=>[],
            'passport_no'=>[],
            'contact_person'=>[],
            'note1'=>[],
            'note2'=>[],
            'note3'=>[],
            'note4'=>[],
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
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

    /*
     * custom failedValidation response
     */
    public function failedValidation ( Validator $validator )
    {
        throw new HttpResponseException(response()->json(
            [
                'status'    => 422,

                'success'   => false,

                'message'   => __ ('validation errors'),

                'data'      => $validator->errors()
            ],
            422
        ));
    }

    /*
     * translate failedValidation messages
     */
    public function messages ()
    {
        return [
            'required'=>__ ('required'),
            'unique'=>__ ('exists')
        ];
    }
}
