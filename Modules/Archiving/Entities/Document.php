<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_documents';
    protected $guarded = ['id'];

    public function archDocType()
    {
        return $this->belongsTo(DocType::class, 'arch_doc_type', 'id');
    }
    public function docStatus()
    {
        return $this->belongsTo(DocStatus::class, 'doc_status', 'id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function hasChildren()
    {
        return $this->departments()->count() > 0 ||
        $this->children()->count() > 0 ||
        $this->archDocType()->count() > 0;
    }

}
