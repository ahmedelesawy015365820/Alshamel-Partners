<?php

namespace App\Http\Resources\DocumentHeaderDetail;

use App\Http\Resources\GeneralItemResource;
use App\Http\Resources\Governorate\GovernorateResource;
use App\Http\Resources\ItemBreakDown\ItemBreakDownResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\PackageResource;
use Modules\Booking\Transformers\UnitResource;

class DocumentHeaderDetailReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'document_header_id' => $this->document_header_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'quantity' => $this->quantity,
            'price_per_uint' => $this->price_per_uint,
            'total' => $this->total,
            'unit_type' => $this->unit_type,
            'rent_days' => $this->rent_days,
            'unit_id' => $this->unit_id,
            'item_id' => $this->item_id,
            'check_in_time' => $this->check_in_time,
            'discount' => $this->discount,
            'document_header'    =>  $this->documentHeader ,
            'unit' => $this->unit,
            'item' => $this->item,
        ];
    }
}
