<?php

namespace Modules\BoardsRent\Transformers\LookUp;

use Illuminate\Http\Resources\Json\JsonResource;

class AllBRentLookUpResource extends JsonResource
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

            "id"          => $this->id,
            "name"        => $this->name,
            "name_e"      => $this->name_e,
            "type"        => $this->type,
            'is_parent'   => $this->is_parent,
            "is_children" => $this->is_children,
            "parent_id"   => $this->parent_id,
            "children"    => ChildrenLookUpResource::collection($this->children),

        ];
    }
}
