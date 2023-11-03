<?php

namespace Modules\Archiving\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveFileFavourite extends Model
{
    use HasFactory;
    protected $table = 'arch_archive_file_favourite';
    protected $guarded = ['id'];
    // protected $fillable = [
    //     'arch_archive_file_id',
    //     'user_id',
    //     'admin_id',
    // ];

    public function archiveFile()
    {
        return $this->belongsTo(ArchiveFile::class,"arch_archive_file_id");
    }

}
