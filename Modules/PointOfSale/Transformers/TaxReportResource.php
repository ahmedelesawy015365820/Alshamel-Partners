<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxReportResource extends JsonResource
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
            'invoice_id' => $this->prefix ?? $this->invoice_id,
            'date' => $this->date,
            'type' => $this->order_type,
            'branch_name' => $this->branch->name ?? null,
            'branch_name_e' => $this->branch->name_e ?? null,
            'total' => $this->total,
            'tax' => $this->total_tax,
        ];
    }
}
