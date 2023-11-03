<?php

namespace App\Http\Resources\VoucherHeader;

use App\Http\Resources\Branch\RelationBranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\Employee\RelationEmployeeResource;
use App\Http\Resources\GeneralCustomer\RelationGeneralCustomerResource;
use App\Http\Resources\PaymentMethod\RelationPaymentMethodResource;
use App\Http\Resources\Serials\RelationSerialResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherHeaderResource extends JsonResource
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
            'id'                => $this->id,
            'document_id'       => $this->document_id,
            'branch_id'         => $this->branch_id,
            'date'              => $this->date,
            'serial_id'         => $this->serial_id,
            'salesmen_id'       => $this->salesmen_id,
            'customer_id'       => $this->customer_id,
            'payment_method_id' => $this->payment_method_id,
            'amount'            => $this->amount,
            'serial_number'     => $this->serial_number,
            'client_type_id'    => $this->client_type_id,
            'module_type_id'    => $this->module_type_id,
            'prefix'            => $this->prefix,
            'branch'            => $this->branch,
            'document'          => $this->document,
            'salesmen'          => $this->salesmen,
            'customer'          => $this->customer,
            'serial'            => $this->serial,
            'paymentMethod'     => $this->paymentMethod

        ];
    }
}
