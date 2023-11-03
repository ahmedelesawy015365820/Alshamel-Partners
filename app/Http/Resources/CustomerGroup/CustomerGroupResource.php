<?php

namespace App\Http\Resources\CustomerGroup;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerGroupResource extends JsonResource
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
            'title' => $this->title,
            'title_e' => $this->title_e,
            'discount' => $this->discount,
            'is_default' => $this->is_default,

        ];
    }
}
