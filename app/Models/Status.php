<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\UnitStatus;
use Spatie\Activitylog\LogOptions;

class Status extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_statuses';

    protected $guarded = ['id'];

    public function unitStatuses()
    {
        return $this->hasMany(UnitStatus::class, 'status_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function documentCompanyModuleStatuses()
    {
        return $this->hasMany(DocumentCompanyModuleStatus::class,'status_id');

    }

    public function hasChildren()
    {
        return $this->unitStatuses()->count() > 0 ||
            $this->orders()->count() > 0 ||  $this->documentCompanyModuleStatuses->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Status')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
