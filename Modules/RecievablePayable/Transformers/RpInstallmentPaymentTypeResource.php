<?php

namespace Modules\RecievablePayable\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RpInstallmentPaymentTypeResource extends JsonResource
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
            "name"=>$this->name,
            "name_e"=>$this->name_e,
            "installment_payment_type_freq"=>$this->installment_payment_type_freq,
            "is_partially"=>$this->is_partially,
            "is_passed"=>$this->is_passed,
            "is_passed_all"=>$this->is_passed_all,
            "is_passed_contract_plan"=>$this->is_passed_contract_plan,
            "auto_freq"=>$this->auto_freq,
            "freq_period"=>$this->freq_period,
            "step"=>$this->step,
            "is_conditional"=>$this->is_conditional,
            "installment_condation_id"=>$this->installment_condation_id,
            "installment_condation"=>$this->installment_condation,
            "payment_plan_installment"=>$this->payment_plan_installment,
        ];
    }
}
