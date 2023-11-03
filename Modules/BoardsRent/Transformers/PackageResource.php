<?php

namespace Modules\BoardsRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\Panel\RelationBRentPanelResource;

class PackageResource extends JsonResource
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
            'id'     => $this->id,
            'name'   => $this->name,
            'name_e' => $this->name_e,
            'code'   => $this->code,
            'price'  => $this->price,
            "panels" => RelationBRentPanelResource::collection($this->panels),
        ];
    }
}
