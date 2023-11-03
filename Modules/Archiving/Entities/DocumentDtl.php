<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDtl extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_documents_dtl';

    protected $guarded = ['id'];

    public function archDocType()
    {
        return $this->belongsTo(DocType::class, 'doc_type_id', 'id');
    }

}
