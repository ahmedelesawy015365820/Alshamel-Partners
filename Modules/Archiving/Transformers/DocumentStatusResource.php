<?php

namespace Modules\Archiving\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Entities\DocStatus;
use Modules\Archiving\Entities\Document;

class DocumentStatusResource extends JsonResource
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
            'document_id'=>$this->document_id,
            'status_id'=>$this->status_id,
            'document' => new DocumentResource(Document::find($this->document_id)),
            'status' => new DocStatusResource(DocStatus::find($this->status_id)),
        ];
    }
}
