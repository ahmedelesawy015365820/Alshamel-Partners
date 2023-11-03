<?php

namespace Modules\HR\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestSearchResource extends JsonResource
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
            'employee_id' => (int) $this->employee_id,
            'request_datetime' => $this->request_datetime,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'remark' => $this->remark,
            'amount' => (double) $this->amount,
            'from_hour' => $this->from_hour,
            'to_hour' => $this->to_hour,
            'approved_from_date' => $this->approved_from_date,
            'approved_to_date' => $this->approved_to_date,
            'approved_from_hour' => $this->approved_from_hour,
            'approved_to_hour' => $this->approved_to_hour,
            'approved_amount' => (double) $this->approved_amount,
            'approved_date' => $this->approved_date,
            'request_types_id' => (int) $this->request_types_id,
            'request_status_id' => (int) $this->request_status_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
