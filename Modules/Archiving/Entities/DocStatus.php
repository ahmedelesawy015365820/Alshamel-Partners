<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocStatus extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_doc_statuses';
    protected $guarded = ['id'];
    // protected $fillable = [
    //     'name',
    //     'name_e',
    // ];

    // relations

    public function files()
    {
        return $this->belongsToMany(ArchiveFile::class, 'arch_archive_files', 'arch_doc_status_id', 'arch_doc_type_id');
    }

    public function hasChildren()
    {
        return $this->files()->count() > 0;
    }

}
