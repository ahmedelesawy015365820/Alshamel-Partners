<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseSummaryReportResource extends JsonResource
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
            'name' => $this->name ?? $this->title,
            'name_e' => $this->name_e ?? $this->title_e,
            'item_sold' => $this->item_sold,
            'sub_total' => $this->sub_total,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'item_purchased' => $this->item_purchased,
            'total_purchased' => $this->total_purchased,
        ];
    }
}
