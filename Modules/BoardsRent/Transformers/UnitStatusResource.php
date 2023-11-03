<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\Status\RelationStatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitStatusResource extends JsonResource
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
            'status_id'  => $this->status_id,
            'status'     => new RelationStatusResource($this->status),
        ];

    }
}
