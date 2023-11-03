<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Transformers\RlstWalletResource;

class RlstBuildingWalletResource extends JsonResource
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
            "id"                   => $this->id,
            "wallet"               => $this->whenLoaded('wallet'),
            "building"             => $this->whenLoaded('building')
        ];
    }
}
