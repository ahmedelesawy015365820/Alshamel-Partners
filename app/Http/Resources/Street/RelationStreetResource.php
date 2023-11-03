<?php

namespace App\Http\Resources\Street;

use Illuminate\Http\Resources\Json\JsonResource;

class RelationStreetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id'     => $this->id,
            'name'   => $this->name,
            'name_e' => $this->name_e,
        ];
    }
}
