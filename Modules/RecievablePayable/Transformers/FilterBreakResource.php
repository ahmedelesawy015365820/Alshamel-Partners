<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Transformers\RlstContractResource;
use Modules\RealEstate\Transformers\RlstInvoiceResource;

class FilterBreakResource extends JsonResource
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
            "terms"=>$this->terms,
            "invoice_id"=>$this->invoice_id,
            "document"=>$this->document,
            "currency"=>$this->currency,
            "customer"=>$this->customer,
            "installment_status"=>$this->installment_status,
            "installment_payment_type"=>$this->installment_payment_type,
            "openingBalance"=>$this->openingBalance,
            "contract"=> new RlstContractResource($this->contract),
            "payment_invoice"=> new RlstInvoiceResource($this->paymentInvoice),
            "reservation"=>$this->reservation,
            "amount_status"=>$this->amount_status,
        ];
    }
}
