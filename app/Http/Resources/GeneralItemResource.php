<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Booking\Transformers\Unit\RelationBookingUnitResource;
use Modules\Booking\Transformers\Unit\RelationUnitResource;
use Modules\Booking\Transformers\UnitResource;

class GeneralItemResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'price' => $this->price,
            'code_number' => $this->code_number,
            'type' => $this->type,
            'unit_id' => new RelationBookingUnitResource($this->unit),
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];







    }
}
