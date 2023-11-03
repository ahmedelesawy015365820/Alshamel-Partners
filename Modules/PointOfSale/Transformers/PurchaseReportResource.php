<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReportResource extends JsonResource
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
            'supplier_name' => $this->supplier->name ?? '',
            'supplier_name_e' => $this->supplier->name_e ?? '',
            'created_by' => $this->created_by,
            'item_purchased' => $this->items->count(),
            'total' => $this->total,
            'due_amount' => $this->due_amount,
            'order_items' => $this->items,
        ];
    }
}
