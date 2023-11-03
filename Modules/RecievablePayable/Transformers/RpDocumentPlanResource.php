<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RpDocumentPlanResource extends JsonResource
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
            "document_id"=>$this->document_id,
            "plan_id"=>$this->plan_id,
            "install_payment_plan"=>$this->installPaymentPlan
        ];
    }
}
