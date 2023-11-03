<?php

namespace App\Http\Resources\Serials;

use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Branch\RelationBranchResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\RestartPeriod\RelationRestartPeriodResource;
use App\Http\Resources\RestartPeriodResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SerialResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'company_id' => $this->company_id,
            'start_no' => $this->start_no,
            'document_id' => $this->document_id,
            'perfix' => $this->perfix,
            'suffix' => $this->suffix,
            'restart_period_id' => $this->restart_period_id,
            "store" => $this->store,
            "has_child" => $this->has_child,
            "document" => $this->document,
            "branch" => $this->branch,
            'restart_period' => $this->restartPeriod,
            "gender" => $this->gender
        ];
    }
}
