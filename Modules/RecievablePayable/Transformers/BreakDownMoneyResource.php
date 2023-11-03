<?php

namespace Modules\RecievablePayable\Transformers;

use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use Illuminate\Http\Resources\Json\JsonResource;

use Modules\RealEstate\Transformers\RlstContractResource;
use Modules\RealEstate\Transformers\RlstInvoiceResource;

class BreakDownMoneyResource extends JsonResource
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
            "id"               => $this->id,
            "customer_id"      => $this->customer_id,
            "break_type"       => $this->break_type,
            "break_id"         => $this->break_id,
            "debit"            => $this->debit,
            "credit"           => $this->credit,
            "instalment_date"  => $this->instalment_date,
            "total"            => $this->total,
            "prev_settlement"  => $this->balance ?? 0,
            "documentHeader"   => $this->whenLoaded('documentHeader'),
            "document"         => $this->whenLoaded('document'),
        ];

    }
}
