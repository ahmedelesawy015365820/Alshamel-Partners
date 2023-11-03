<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Status;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmMemberSetting extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = [];
    protected $table = 'cm_members_settings';


    public function memberType()
    {
        return $this->belongsTo(CmMemberType::class,'member_type_id');
    }

    public function financialStatus()
    {
        return $this->belongsTo(CmFinancialStatus::class,'financial_status_id');
    }

    public function generalStatus()
    {
        return $this->belongsTo(Status::class,'general_status_id');
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmMemberPermission')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
