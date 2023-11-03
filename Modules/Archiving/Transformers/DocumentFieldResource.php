<?php

namespace Modules\Archiving\Transformers;

use App\Http\Resources\DataTypeResource;
use App\Http\Resources\TreeProperty\TreePropertyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentFieldResource extends JsonResource
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
            "data_type" => new DataTypeResource($this->dataTye),
            "tree_property" => new TreePropertyResource($this->treeProperty),
            "tree_property_id" => $this->tree_property_id,
            "parent_id" => $this->parent_id,
            'is_reference' => $this->is_reference,
            'lookup_table' => $this->lookup_table,
            'lookup_table_column' => $this->lookup_table_column,
            "field_characters" => $this->field_characters,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
