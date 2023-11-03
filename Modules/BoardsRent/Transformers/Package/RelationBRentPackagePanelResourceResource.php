<?php

namespace Modules\BoardsRent\Transformers\Package;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\Panel\RelationBRentPanelResource;

class RelationBRentPackagePanelResourceResource extends JsonResource
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
            "panels" => RelationBRentPanelResource::collection($this->panels),
        ];
    }
}
