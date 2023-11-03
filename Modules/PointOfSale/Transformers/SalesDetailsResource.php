<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesDetailsResource extends JsonResource
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
            'invoice_id'=> $this->order ? $this->order->prefix : null,
            'sub_total'=> $this->sub_total,
            'quantity' => $this->quantity,
            'product_title' => $this->product ? $this->product->title : null,
            'type' => $this->type,
            'order_type'=> $this->order ? $this->order->order_type : null,
            'status'=> $this->order ? $this->order->status : null,
        ];
    }
}
