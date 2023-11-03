<?php

namespace App\Http\Resources\Avenue;

use Illuminate\Http\Resources\Json\JsonResource;

class AvenueResource extends JsonResource
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
            'is_active' => $this->is_active,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'country' => $this->country,

        ];
    }
}
