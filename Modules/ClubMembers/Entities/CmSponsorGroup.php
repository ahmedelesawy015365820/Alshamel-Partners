<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class CmSponsorGroup extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = [];
    protected $table = 'cm_sponsors_group';

    public function sponsors()
    {
        return $this->hasMany(CmSponser::class, 'group_id', 'id');
    }

    public function totalSponsors()
{
    return $this->sponsors()->count();
}

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('cm sponsors group')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
