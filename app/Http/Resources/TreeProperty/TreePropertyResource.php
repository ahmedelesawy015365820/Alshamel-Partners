<?php

namespace App\Http\Resources\TreeProperty;

use Illuminate\Http\Resources\Json\JsonResource;

class TreePropertyResource extends JsonResource
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
            "parent"=>$this->parent,
            'parent_id'=>$this->parent_id,
            'screen_id'=>$this->screen_id,
            'required'=>$this->required,
            'screen_node'=>$this->screen_node,
        ];
    }
}
