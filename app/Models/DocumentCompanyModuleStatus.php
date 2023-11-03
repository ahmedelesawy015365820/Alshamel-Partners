<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCompanyModuleStatus extends Model
{
    use HasFactory,LogTrait;

    protected $table = 'general_document_company_module_statuses';

    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query
            ->select('id',
                'document_id',
                'document_module_type_id',
                'status_id',
                'company_id')->with(['document:id,name,name_e','status:id,name,name_e','documentModuleType:id,name,name_e']);
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }
    public function documentModuleType()
    {
        return $this->belongsTo(DocumentModuleType::class,'document_module_type_id');
    }










    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document Company Module Status')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }}
