<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RpScreenSubContactGroupResource extends JsonResource
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
            "id" => $this->id,
            "sub_contract_group_id" => $this->sub_contract_group_id,
            "screen_id" => $this->screen_id,
            "subContactGroup" => $this->subContactGroup
        ];
    }
}
