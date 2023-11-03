<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocTypeField extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_doc_type_fields';
    protected $fillable = [
        'doc_type_id',
        'doc_field_id',
        'field_order',
        'is_required',
        'field_characters',
        "parent_id",
        'company_id'
    ];

    // relation
    public function archDocType()
    {
        return $this->belongsTo(DocType::class, 'doc_type_id', 'id');
    }

    public function archField()
    {
        return $this->belongsTo(DocumentField::class, 'doc_field_id', 'id');
    }

    public function canEdit()
    {
        return $this->archDocType->parent_id === $this->archDocType->parent_id;
    }

}
