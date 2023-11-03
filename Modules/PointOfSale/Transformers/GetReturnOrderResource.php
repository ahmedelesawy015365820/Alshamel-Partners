<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class GetReturnOrderResource extends JsonResource
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
            'id'           => $this->id,
            'sub_discount' => $this->sub_discount,
            'tax_type'     => $this->tax_type,
            'date'         => $this->date,
            'order_type'   => $this->order_type,
            'sub_total'    => $this->sub_total,
            'total_tax'    => $this->total_tax,
            'due_amount'   => $this->due_amount,
            'total'        => $this->total,
            'profit'       => $this->profit,
            'status'       => $this->status,
            'all_discount' => $this->all_discount,
            'customer_id'  => $this->customer_id,
            'customer'     => collect($this->customer)->only(['id','name','name_e']),
            'supplier_id'  => $this->supplier_id,
            'supplier'     => collect($this->supplier)->only(['id','name','name_e']),
            'branch_id'    => $this->branch_id,
            'branch'       => collect($this->branch)->only(['id','name','name_e']),
            "prefix"       => $this->prefix,
            'order_items'  => OrderItemResource::collection($this->items),
        ];
    }
}
