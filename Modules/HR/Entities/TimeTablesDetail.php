<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class TimeTablesDetail extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'hr_timetables_detail';
    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query->select('id', 'timetables_header_id', 'day_no', 'shift1_from', 'shift1_to', 'shift2_from', 'shift2_to', 'is_off')
            ->with('timetablesHeader:id,name,name_e');
    }

    public function timetablesHeader()
    {
        return $this->belongsTo(TimeTablesHeader::class, 'timetables_header_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Time Tables Detail')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
