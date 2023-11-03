<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocType extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_doc_types';

    protected $guarded = ['id'];

    public function children()
    {
        return $this->hasMany(DocType::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(DocType::class, 'parent_id', 'id');
    }

    //    public function files()
    //    {
    //        return $this->belongsToMany(ArchiveFile::class, 'arch_archive_files', 'arch_doc_type_id', 'arch_doc_statues_id');
    //    }

    public function statuses()
    {
        return $this->belongsToMany(DocStatus::class, 'arch_document_statuses', 'document_id', 'status_id', 'id', 'id');
    }
    public function archiveFiles()
    {
        return $this->hasMany(ArchiveFile::class, "arch_doc_type_id");
    }



    public function getEmployees()
    {
        $e = $this->archiveFiles()->where('data_type_value', 'like', '');
    }

    //        public function fields()
    //        {
    //            return $this->belongsToMany(DocField::class, 'arch_doc_type_fields', 'doc_type_id', 'doc_field_id')->withPivot('field_order', 'is_required', 'field_characters');
    //        }

    public function hasChildren()
    {
        return $this->children()->count() > 0 ||
        $this->statuses()->count() > 0 ||
        $this->archiveFiles()->count() > 0;
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'arch_type_department', 'arch_doc_type_id', 'arch_department_id')
            ->whereNotNull("key_value");
    }

    public function fields(){

        return $this->belongsToMany(DocumentField::class,'arch_doc_type_fields','doc_type_id','doc_field_id');
    }

    public function getKeyAttribute()
    {
        return $this->fields()->first();
    }

}
