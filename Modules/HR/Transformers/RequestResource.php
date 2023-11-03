<?php

namespace Modules\HR\Transformers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            // 'created_at' => $this->created_at,
            "created_at" => $this->created_at->format('Y-m-d H:i:s'),
            "updated_at" => $this->updated_at->format('Y-m-d H:i:s'),
            'employee_id' => (int) $this->employee_id,
            'employee' => $this->employee,
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
            'request_types' => $this->requestType,
            'request_status' => $this->requestStatus,
            'approved_by' => (int) $this->approved_by,
            'approved' => $this->approved,
            // "media" => $this->media ? $this->transformMedia($this->media) : null,

            "media" => isset($this->files) ? FileResource::collection($this->files) : null,


        ];
    }

    private function transformMedia($files)
    {
        $media = collect();

        foreach ($files as $file) {
            $media->push([
                'name' => $file->name,
            ]);
        }

        return $media->isNotEmpty() ? $media : null;
    }
}
