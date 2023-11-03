<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class OnlineRequest extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = 'hr_online_requests';

    protected $fillable = [
        'request_type_id',
        'employee_id',
        'start_from',
        'end_date',
        'descriptions',
        'amount',
        'from_hour',
        'to_hour',
        'status_id',
        'approved_date',
        'approved_from_date',
        'approved_to_date',
        'approved_from_hour',
        'approved_to_hour',
        'approved_amount',
        'date',
        'admin_comment',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Job Title')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

}
