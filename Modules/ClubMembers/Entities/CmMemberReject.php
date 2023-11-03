<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Branch;
use App\Models\Serial;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CmMemberReject extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

    protected $table = 'cm_member_rejects';


    public function member()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmMember::class, 'cm_member_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function dischargeReson()
    {
        return $this->belongsTo(CmDischargeReson::class, 'discharge_reson_id');
    }
    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }




    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmHistoryTransform')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
