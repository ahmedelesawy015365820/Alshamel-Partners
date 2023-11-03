<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Status;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmHistoryTransform extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'cm_member_history_transform';

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmHistoryTransform')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
