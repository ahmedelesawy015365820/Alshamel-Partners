<?php

namespace App\Http\Resources;

use App\Http\Resources\RestartPeriod\RelationRestartPeriodResource;
use App\Http\Resources\SalesmenPlansSource\RelationSalesmenPlansSourceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesmenPlanResource extends JsonResource
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
            "salesmen_plans_source" => $this->salesmenPlansSource,
            'restart_period' => $this->restartPeriod,
        ];
    }
}
