<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\Salesman\SalesmanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstReservationUnitResource extends JsonResource
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
            "reservation" => new RlstReservationResource($this->reservation),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
