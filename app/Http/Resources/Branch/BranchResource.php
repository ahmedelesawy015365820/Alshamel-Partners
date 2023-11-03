<?php

namespace App\Http\Resources\Branch;

use App\Http\Resources\FileResource;
use App\Http\Resources\Store\RelationStoreResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            "parent" => $this->parent,
            "children" => RelationBranchResource::collection($this->children),
//            "serials" =>  RelationSerialResource::collection($this->serials),
            "serials" => $this->serials,
            "stores" => RelationStoreResource::collection($this->stores),

            'code_country' => $this->code_country,
            'phone' => $this->phone,
            'second_phone' => $this->second_phone,
            'fax' => $this->fax,
            'p_o_pox' => $this->p_o_pox,
            'address' => $this->address,
            'email' => $this->email,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,

        ];
    }
}
