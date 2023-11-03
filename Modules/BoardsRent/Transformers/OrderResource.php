<?php

namespace Modules\BoardsRent\Transformers;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\Salesman\SalesmanResource;
use App\Http\Resources\Serials\SerialResource;
use App\Http\Resources\Status\StatusResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\CustomerResource;
use Modules\BoardsRent\Transformers\SellMethodResource;

class OrderResource extends JsonResource
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
            'date' => $this->date,
            'note' => $this->note,
            'discount' => $this->discount,
            'paid' => $this->paid,
            'due' => $this->due,
            'total' => $this->total,
            'is_stripe' => $this->is_stripe,
            "serial_number" => $this->serial_number,
            "prefix" => $this->prefix,
            "is_quotation" => $this->is_quotation,
            'order_details' => OrderDetailsResource::collection($this->details),
            'branch' => new BranchResource($this->branch),
            'serial' => new SerialResource($this->serial),
            'status' => new StatusResource($this->status),
            'customer' => new CustomerResource($this->customer),
            'salesman' => new SalesmanResource($this->salesman),
            'sell_method' => new SellMethodResource($this->sellMethod),
        ];
    }
}
