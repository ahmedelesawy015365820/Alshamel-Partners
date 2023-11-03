<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstWalletResource extends JsonResource
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
            'owners' => $this->whenLoaded('owners'),
            //'create_owners' => RlstOwnerResource::collection($this->whenLoaded('owners'))->only(['id', 'name', 'name_e']),
            "buildings" => $this->whenLoaded('buildings'),
        ];
    }
}
