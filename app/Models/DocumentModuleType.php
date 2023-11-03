<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentModuleType extends Model
{
    use HasFactory,LogTrait;

    protected $table = 'general_document_module_types';

    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query
            ->select('id',
                'name',
                'name_e',
                'title',
                'title_e', 'db_table');
    }


    public function documentCompanyModuleStatuses()
    {
        return $this->hasMany(DocumentCompanyModuleStatus::class,'document_module_type_id');

    }

    public function hasChildren()
    {
        return   $this->documentCompanyModuleStatuses->count() > 0;
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document Module Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
