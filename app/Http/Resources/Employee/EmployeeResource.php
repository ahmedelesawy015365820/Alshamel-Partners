<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_e' => $this->name_e,
            'manager_id' => $this->manager_id,
            'is_salesman' => $this->is_salesman,
            'customer_handel' => $this->customer_handel,
            'department_id' => $this->department_id,
            'code_country' => $this->code_country,
            'salesman_type_id' => $this->salesman_type_id,
            'for_all_customer' => $this->for_all_customer,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'sms' => $this->sms,
            'att_code' => $this->att_code,
            'company_id' => $this->company_id,
            'job_id' => $this->job_id,
            'manage_others' => $this->manage_others,
            'branch_id' => $this->branch_id,
            'job_title' => $this->jobTitle,
            'branch' => $this->branch,
            'manager' => $this->manager,
            'salesman_type' => $this->salesmanType,
            'department' => $this->depertment,
            'plans' => $this->plans,
            'managers' => $this->managersData->map(function ($manager) {
                return [
                    'id' => $manager->manager_id,
                    'name' => $manager->manager_name,
                    'name_e' => $manager->manager_name_e,
                ];
            })->toArray(),

        ];
    }
}
