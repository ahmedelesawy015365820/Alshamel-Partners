<?php

namespace App\Http\Resources\ExternalSalesmen;

use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Country\RelationCountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalSalesmenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'phone' => $this->phone,
            'address' => $this->address,
            'rp_code' => $this->rp_code,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'national_id' => $this->national_id,
            'country' => $this->country,
    
        ];
    }
}
