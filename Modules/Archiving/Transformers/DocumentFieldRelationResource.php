<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentFieldRelationResource extends JsonResource
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
            'id'   => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            "data_type" => $this->dataTye,
            'is_reference' => $this->is_reference,
            'lookup_table' => $this->lookup_table,
            'lookup_table_column' => $this->lookup_table_column,
            'tree_property_id' => $this->tree_property_id,
        ];
    }
}
