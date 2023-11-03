<?php

namespace Modules\ClubMembers\Entities;


use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmMemberType extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];
    protected $table = 'cm_members_types';
    
    public function memberPermissions()
    {
        return $this->belongsToMany(CmMemberPermission::class, 'cm_type_permissions', 'cm_members_type_id', 'cm_permissions_id');
    }

    public function parent()
    {
        return $this->belongsTo(CmMemberType::class,'parent_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmMemberType')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
