<?php

namespace App\Http\Resources\DocumentHeader;

use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AllDocumentHeaderResource extends JsonResource
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
            'id'                       => $this->id,
            'document_status_id'       => $this->document_status_id,
            'reason'                   => $this->reason,
            'branch_id'               => $this->branch_id,
            'date'                     => $this->date,
            'serial_number'            => $this->serial_number,
            'prefix'                   => $this->prefix,
            'serial_id'                => $this->serial_id,
            'document_id'              => $this->document_id,
            'related_document_prefix'  => $this->related_document_prefix,
            'related_document_id'      => $this->related_document_id,
            'related_document_number'  => $this->related_document_number,
            'sell_method_id'           => $this->sell_method_id,
            'employee_id'              => $this->employee_id,
            'customer_id'              => $this->customer_id,
            'task_id'                  => $this->task_id,
            'external_salesmen_id'     => $this->external_salesmen_id,
            'payment_method_id'        => $this->payment_method_id,
            'customer_type'            => $this->customer_type,
            'total_invoice'            => $this->total_invoice,
            'invoice_discount'         => $this->invoice_discount,
            'net_invoice'              => $this->net_invoice,
            'remaining'                => $this->remaining,
            'complete_status'          => $this->complete_status,
            'unrelaized_revenue'       => $this->unrelaized_revenue,
            'sell_method_discount'     => $this->sell_method_discount,
            'external_commission'      => $this->external_commission,

            'revenue'                 => $this->revenue,
            'unrealized_commission'   => $this->unrealized_commission,
            'commission'              => $this->commission,
            'total_depit_note'        => $this->total_depit_note,


            'branch'                  => collect($this->branch)->only(['id','name','name_e']),
            'employee'                => collect($this->employee)->only(['id','name','name_e']),
            'customer'                => collect($this->customer)->only(['id','name','name_e']),



        ];
    }
}
