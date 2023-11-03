<?php

namespace App\Http\Resources;

use App\Http\Resources\Location\RelationLocationResource;
use App\Http\Resources\Priority\RelationPriorityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            "priority_id" => $this->priority_id,
            'parent_id' => $this->parent_id,
            'parent' =>$this->parent,
            "priority" =>$this->priority,

        ];
    }
}
