<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RecievablePayable\Transformers\RpPaymentPlanInstallmentResource;

class RlstContractDetailsResource extends JsonResource
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
            "unit_value" => $this->unit_value,
            "reservation_value" => $this->reservation_value,
            "building" => new RlstBuildingResource($this->building),
            "building_id" => $this->building_id,
            "unit" => new RlstUnitResource($this->unit),
            "unit_id" => $this->unit_id,
            "installment_payment_plans_id" => $this->installment_payment_plans_id,
            "installment_payment_plans" => new RpPaymentPlanInstallmentResource($this->planInstallment),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
