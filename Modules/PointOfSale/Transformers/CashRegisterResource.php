<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CashRegisterResource extends JsonResource
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
            'title' => $this->title,
            'title_e' => $this->title_e,
            'branch_id' => $this->branch_id,
            'created_by' => $this->created_by,
            'multiple_access' => $this->multiple_access,
            'branch' => $this->branch,
        ];
    }
}
