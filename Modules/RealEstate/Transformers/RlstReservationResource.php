<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Http\Resources\Salesman\SalesmanResource;
use App\Http\Resources\Serials\SerialResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstReservationResource extends JsonResource
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
            "date" => $this->date,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "serial_number" => $this->serial_number,
            "prefix" => $this->prefix,
            "branch" => new BranchResource($this->branch),
            "serial" => new SerialResource($this->serial),
            "customer" => new GeneralCustomerResource($this->customer),
            "salesman" => new SalesmanResource($this->salesman),
            "details" => RlstReservationDetailResource::collection($this->details),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
