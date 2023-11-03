<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\BankAccount\BankAccountResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstOwnerResource extends JsonResource
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
            'rb_code' => $this->rb_code,
            'phone_code' => $this->phone_code,
            "contact_person" => $this->contact_person,
            "contact_phones" => $this->contact_phones,
            "national_id" => $this->national_id,
            "whatsapp" => $this->whatsapp,
            "categories" => $this->categories,
            "attachments" => $this->attachments,
            "country" => $this->whenLoaded('country'),
            "city" => $this->whenLoaded('city'),
            "nationality" => $this->whenLoaded('country'),
            "bank_account" => $this->whenLoaded('bankAccount'),

        ];
    }
}
