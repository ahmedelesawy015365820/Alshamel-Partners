<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Http\Resources\Salesman\SalesmanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstInvoiceResource extends JsonResource
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
            "branch" => new BranchResource($this->branch),
            "customer" => new RlstCustomerResource($this->customer),
            "salesman" => new SalesmanResource($this->salesman),
            "paymentMethod" => $this->paymentMethod,
            "date" => $this->date,
            "serial_number" => $this->serial_number,
            "prefix" => $this->prefix,
            "invoice_details" => $this->invoiceItems,


            // "serial"=> new SerialResource($this->serial),
            // "payment_plan_id" => $this->payment_plan_id,
            // "start_date" => $this->start_date,
            // "end_date" => $this->end_date,
            // "flat_amount" => $this->flat_amount,
            // "paid_amount" => $this->paid_amount,
            // "created_at" => $this->created_at,
            // "updated_at" => $this->updated_at,
            // "document" => new DocumentResource($this->document),
            // "building"=> new RlstBuildingResource($this->building),
            // "unit"=> new RlstUnitResource($this->unit),
            // "plan_installment" => new RpPaymentPlanInstallmentResource($this->planInstallment),
            // "payment_plan" => @new \Modules\RecievablePayable\Transformers\RpInstallmentPaymentPlanResource($this->paymentPlan),
        ];
    }
}
