<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'phone' => $this->phone,
            'email' => $this->email,
            'country_id' => $this->country_id,
            "country" => new CountryResource($this->country),
            'city_id' => $this->city_id,
            "city" => new CityResource($this->city),
            'nationality_id' => $this->nationality_id,
            "nationality" => new CountryResource($this->nationality),
            'contact_person' => $this->contact_person,
            'contact_phones' => $this->contact_phones,
            'national_id' => $this->national_id,
            'passport_no' => $this->passport_no,
            'note1' => $this->note1,
            'note2' => $this->note2,
            'note3' => $this->note3,
            'note4' => $this->note4,
            'bank_account_id' => $this->bank_account_id,
            "bank_account" => new \App\Http\Resources\BankAccount\BankAccountResource($this->bankAccount),
            'whatsapp' => $this->whatsapp,
            'categories' => $this->categories,
            "attachments" => $this->attachments,
            'rb_code' => $this->rb_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
