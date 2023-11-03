<?php

namespace App\Http\Resources\ScreenTreeProperty;

use App\Http\Resources\Priority\RelationPriorityResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreenTreePropertyResource extends JsonResource
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
            'screen_id' => $this->screen_id,
            'property_id' => $this->property_id,
            "tree_property" => new RelationPriorityResource($this->property)
        ];
    }
}
