<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocTypeDepartment extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_type_department';

    protected $guarded = ['id'];

    public function department ()
    {
        return $this->belongsTo(Department::class,"arch_department_id");
    }

    public function docType ()
    {
        return $this->belongsTo(DocType::class,"arch_doc_type_id");
    }

}
