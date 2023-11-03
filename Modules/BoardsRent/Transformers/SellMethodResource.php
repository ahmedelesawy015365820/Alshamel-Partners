<?php

namespace Modules\BoardsRent\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SellMethodResource extends JsonResource
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
            'id'                       => $this->id,
            'name'                     => $this->name,
            'name_e'                   => $this->name_e,
            'commission_ratio'         => $this->commission_ratio,
            'target_calculation_ratio' => $this->target_calculation_ratio,
            'is_all_value'             => $this->is_all_value,
            'is_default'               => $this->is_default,
        ];
    }
}
