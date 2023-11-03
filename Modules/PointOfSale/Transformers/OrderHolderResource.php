<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderHolderResource extends JsonResource
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
            'sub_discount' => $this->sub_discount,
            'tax_type' => $this->tax_type,
            'date' => $this->date,
            'sales_note' => $this->sales_note,
            'total_tax' => $this->total_tax,
            'due_amount' => $this->due_amount,
            'total' => $this->total,
            'sub_total' => $this->sub_total,
            'type' => $this->type,
            'status' => $this->status,
            'all_discount' => $this->all_discount,
            'customer_id' => $this->customer_id,
            'branch_id' => $this->branch_id,
            'customer' => $this->customer,
            "document_id" => $this->document_id,
            "serial_number" => $this->serial_number,
            "prefix" => $this->prefix,
            'order_items' => OrderItemHoldResource::collection($this->items),
        ];
    }
}
