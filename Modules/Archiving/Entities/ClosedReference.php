<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosedReference extends Model
{
    use HasFactory, LogTrait, SoftDeletes;
    protected $table = 'arch_closed_references';
    protected $guarded = ['id'];

    public function documentField()
    {
        return $this->belongsTo(DocumentField::class, 'docfields_id', 'id');
    }



}
