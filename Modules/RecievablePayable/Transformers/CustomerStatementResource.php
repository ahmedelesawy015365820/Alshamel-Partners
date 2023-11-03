<?php

namespace Modules\RecievablePayable\Transformers;

use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use Illuminate\Http\Resources\Json\JsonResource;

use Modules\RealEstate\Transformers\RlstContractResource;
use Modules\RealEstate\Transformers\RlstInvoiceResource;

class CustomerStatementResource extends JsonResource
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
            "debit"=>$this->debit,
            "credit"=>$this->credit,
            "instalment_date"=>$this->instalment_date,
            "total"=>$this->total,
            "document"=>$this->document,
            "break_type"=>$this->break_type,
            "documentHeader"=>$this->documentHeader,
//            "total_credit" => $this->total_credit,
//            "total_debit" => $this->total_debit,
//            "balance" => ($this->total_debit - $this->total_credit) + $this->debit - $this->credit,
//            "previous_balance" => $this->total_debit - $this->total_credit,
//            "openingBalance"=>$this->openingBalance,
            "contract"=> new RlstContractResource($this->contract),
            "invoice"=> new RlstInvoiceResource($this->invoice),
            "reservation"=>$this->reservation,
        ];
    }
}
