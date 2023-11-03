<?php

namespace App\Http\Resources;

use App\Http\Resources\Priority\RelationPriorityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PriorityResource extends JsonResource
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
            'parent' => $this->parent,
            'parent_id' => $this->parent_id,
            'is_valid' => $this->is_valid,
            'is_default' => $this->is_default,

        ];
    }
}
