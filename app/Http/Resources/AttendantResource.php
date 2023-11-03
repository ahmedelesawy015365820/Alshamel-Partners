<?php

namespace App\Http\Resources;

use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendantResource extends JsonResource
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
            'national_id' => $this->national_id,
            'id_number' => $this->id_number,
            'notes' => $this->notes,
            'customer_id' => $this->customer_id,
            'customer' => $this->customer
        ];


    }
}
