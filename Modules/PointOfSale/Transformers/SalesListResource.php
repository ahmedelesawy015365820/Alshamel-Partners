<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesListResource extends JsonResource
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
            'invoice_id' => $this->prefix,
            'date' => $this->date,
            'type' => $this->type,
            'created_by' => $this->created_by,
            'sold_to' => 'Walk-in customer',
            'status' => 'paid',
            'item_purchased' => $this->items->count(),
            'tax' => $this->total_tax,
            'discount' => $this->all_discount,
            'total' => $this->total,
            'due_amount' => $this->due_amount,
            'order_items' => $this->items,

        ];
    }
}
