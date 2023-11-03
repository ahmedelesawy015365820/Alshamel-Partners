<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesPurchasesReportResource extends JsonResource
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
            'total'=>$this->where('order_type', 'sales')->where('status', 'done')->where('type', 'customer')->where('returned_invoice', null)->sum('total')
        ];
    }

    
}
