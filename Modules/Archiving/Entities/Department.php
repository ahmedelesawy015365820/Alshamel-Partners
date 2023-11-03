<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_departments';
    protected $guarded = ["id"];

    protected $casts = [
        'parent_id' => 'integer',
        'is_active' => \App\Enums\IsActive::class,
    ];

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id', 'id');
    }

    public function archiveFile()
    {
        return $this->hasOne(ArchiveFile::class, 'arch_department_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id', 'id');
    }

    public function documents()
    {
        return $this->belongsToMany(DocType::class, 'arch_type_department', 'arch_department_id', 'arch_doc_type_id');
    }

    public function arch_documents()
    {
        return $this->belongsToMany(DocType::class, 'arch_type_department', 'arch_department_id', 'arch_doc_type_id')
            ->distinct()->whereNull('arch_doc_types.parent_id');
    }

    public function hasChildren()
    {
        return $this->children()->count() > 0 ||
        $this->documents()->count() > 0 ||
        $this->archiveFile()->count() > 0;
    }
}
