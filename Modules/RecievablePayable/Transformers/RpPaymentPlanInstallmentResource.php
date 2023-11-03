<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RpPaymentPlanInstallmentResource extends JsonResource
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
            "installment_payment_plan_id"=>$this->installment_payment_plan_id,
            "installment_payment_type_id"=>$this->installment_payment_type_id,
            "v_date"=>$this->v_date,
            "due_date"=>$this->due_date,
            "total_amount"=>$this->total_amount,
            "paid_amount"=>$this->paid_amount,
            "installment_status_id"=>$this->installment_status_id,
            "doc_type_id"=>$this->doc_type_id,
            "ref_id"=>$this->ref_id,
            "rp_code"=>$this->rp_code,
            "note_a"=>$this->note_a,
            "note_e"=>$this->note_e,
            "is_fixed"=>$this->is_fixed,
            "day_month"=>$this->day_month,
            "installment_payment_plan"=>$this->installment_payment_plan,
            "installment_status"=>$this->installment_status,
            "installment_payment_type"=>$this->installment_payment_type,
            "document_type" => $this->document,


        ];
    }
}

