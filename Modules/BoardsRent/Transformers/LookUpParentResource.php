<?php

namespace Modules\BoardsRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LookUpParentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "name_e" => $this->name_e,
            "type" => $this->type,
            "parent"=>$this->parent,
            'parent_id'=>$this->parent_id,
        ];
    }
}
