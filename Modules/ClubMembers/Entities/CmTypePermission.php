<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Status;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CmTypePermission extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'cm_members_type_id',
        'cm_permissions_id',
        'cm_financial_status_id',
        'membership_period',
        'allowed_subscription_date',
        'allowed_vote_date',
        'company_id',
    ];

    protected $table = 'cm_type_permissions';

    public function type()
    {
        return $this->belongsTo(CmMemberType::class, 'cm_members_type_id');
    }


    public function permission()
    {
        return $this->belongsTo(CmMemberPermission::class, 'cm_permissions_id');
    }


    public function financialStatus()
    {
        return $this->belongsTo(CmFinancialStatus::class, 'cm_financial_status_id');
    }



    public function memberTypes()
    {
        return $this->belongsToMany(CmMemberType::class, 'cm_members_types_permissions', 'member_permission_id', 'member_type_id');
    }



    public function hasChildren()
    {
        return false;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Type Permission')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


    // protected function CmPermissionsId(): Attribute
    // {
    //     return Attribute::make(
    //         set:fn($value) => json_encode($value),
    //         get:fn($value) => json_decode($value)
    //     );
    // }
}
