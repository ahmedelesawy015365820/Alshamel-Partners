<?php

namespace Modules\PointOfSale\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_type' => $this->order_type,
            'sales_note' => $this->sales_note,
            'sub_total' => $this->sub_total,
            'total_tax' => $this->total_tax,
            'due_amount' => $this->due_amount,
            'total' => $this->total,
            'type' => $this->type,
            'profit' => $this->profit,
            'status' => $this->status,
            'all_discount' => $this->all_discount,
            'customer_id' => $this->customer_id,
            'customer' => $this->customer,
            'supplier_id' => $this->supplier_id,
            'supplier' => $this->supplier,
            'branch_id' => $this->branch_id,
            'branch' => $this->branch,
            'transfer_branch_id' => $this->transfer_branch_id,
            'table_id' => $this->table_id,
            'created_by' => $this->created_by,
            'returned_invoice' => $this->returned_invoice,
            'return_type' => $this->return_type,
            'invoice_id' => $this->invoice_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "document_id"=> $this->document_id,
            "serial_number" => $this->serial_number,
            "prefix" => $this->prefix,
            'order_items'=> $this->items,

        ];
    }
}
