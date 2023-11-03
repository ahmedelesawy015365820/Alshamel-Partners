<?php

namespace Modules\Archiving\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArchDocumentStatus extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];



    public function document(){
        return $this->belongsTo (Document::class,'document_id');
    }

    public function status(){
        return $this->belongsTo (DocStatus::class,'status_id');
    }


    public function hasChildren(){
        return ($this->document ()->exists () || $this->status ()->exists ());
    }
}
