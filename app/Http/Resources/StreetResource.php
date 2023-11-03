<?php

namespace App\Http\Resources;

use App\Http\Resources\Avenue\AvenueResource;
use App\Http\Resources\Avenue\RelationAvenueResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StreetResource extends JsonResource
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
            'avenue' =>$this->avenue,
            'is_active' => $this->is_active,
        ];
    }
}
