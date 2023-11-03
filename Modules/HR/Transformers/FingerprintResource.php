<?php

namespace Modules\HR\Transformers;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class FingerprintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $employee = Employee::where('att_code', $this->att_code)->first();

        return [
            'id' => $this->id,
            'name' => $employee->name ?? null,
            'name_e' => $employee->name_e ?? null,
            'att_code' => $this->att_code,
            'vdate' => $this->vdate,
            'att_type' => $this->att_type == 0 ? 'حضور' : 'انصراف',
            'machine_id' => $this->machine_id,

        ];
    }
}
