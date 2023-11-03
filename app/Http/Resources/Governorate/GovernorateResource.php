<?php

namespace App\Http\Resources\Governorate;

use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
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
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            "phone_key" => $this->phone_key,
            "country" => $this->country,
        ];
    }
}
