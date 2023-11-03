<?php

namespace Modules\BoardsRent\Transformers\UnitStatus;

use Illuminate\Http\Resources\Json\JsonResource;

class RelationBRentUnitStatusResource extends JsonResource
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
            'id'         => $this->id,
            'name'       => $this->name,
            'name_e'     => $this->name_e,
        ];
    }
}
