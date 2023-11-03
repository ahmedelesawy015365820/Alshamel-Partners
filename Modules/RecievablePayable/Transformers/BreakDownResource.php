<?php

namespace Modules\RecievablePayable\Transformers;

use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use Illuminate\Http\Resources\Json\JsonResource;

use Modules\RealEstate\Transformers\RlstContractResource;
use Modules\RealEstate\Transformers\RlstInvoiceResource;

class BreakDownResource extends JsonResource
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
            "id"=>$this->id,
            "rate"=>$this->rate,
            "currency_id"=>$this->currency_id,
            "customer_id"=>$this->customer_id,
            "break_id"=>$this->break_id,
            "break_type"=>$this->break_type,
            "document_id"=>$this->document_id,
            "debit"=>$this->debit,
            "credit"=>$this->credit,
            "instalment_type_id"=>$this->instalment_type_id,
            "repate"=>$this->repate,
            "instalment_date"=>$this->instalment_date,
            "total"=>$this->total,
            "details"=>$this->details,
            "module_type"=>$this->module_type,
            "installment_statu_id"=>$this->installment_statu_id,
            "parent_id"=>$this->parent_id,
            "amount_paid"=>$this->amount_paid,
            "amount_remaining"=>$this->amount_remaining,
            "terms"=>$this->terms,
            "invoice_id"=>$this->invoice_id,
            "amount_status"=>$this->amount_status,
            "client_type_id"=>$this->client_type_id,
            "document"=>$this->document,


            "documentHeader"=>new DocumentHeaderResource($this->documentHeader) ,
//            "currency"=>$this->currency,
//            "customer"=>$this->customer,
//            "installment_status"=>$this->installment_status,
//            "installment_payment_type"=>$this->installment_payment_type,
//            "openingBalance"=>$this->openingBalance,
            "contract"=> new RlstContractResource($this->contract),
            "invoice"=> new RlstInvoiceResource($this->invoice),
            "reservation"=>$this->reservation,
//            "children" => BreakDownResource::collection($this->children)


        ];

    }
}
