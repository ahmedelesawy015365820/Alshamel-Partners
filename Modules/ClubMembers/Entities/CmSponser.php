<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmSponser extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'name_e',
        'parent_id',
        'group_id',
        'company_id',
        'cm_member_id',

    ];

    protected $appends = ['haveChildren'];

    protected $table = 'cm_sponsers';

    public function children()
    {
        return $this->hasMany(CmSponser::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(CmSponser::class, 'parent_id', 'id');
    }

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function members()
    {
        return $this->hasMany(\Modules\ClubMembers\Entities\CmMember::class, 'sponsor_id');
    }

    public function sponsorGroup()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponsorGroup::class, 'group_id', 'id');
    }

    public function totalMembers()
{
    return $this->members()->where('member_status_id', 1)->count();
}

public function totalMembersPermissions($permissions)
{
    return $this->members()
        ->where('member_status_id', 1)
        ->whereIn('members_permissions_id', $permissions->pluck('id'))
        ->count();
}
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sponser')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function hasChildren()
    {
        $h = $this->members()->count() > 0;
        return $h;
    }

    public function sponsorAsMember()
    {
        return $this->has(\Modules\ClubMembers\Entities\CmMember::class, 'cm_member_id','id');
    }
}
