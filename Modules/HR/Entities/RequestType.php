<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RequestType extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'name_e',
        'start_from',
        'end_date',
        'amount',
        'from_hour',
        'to_hour',
    ];

    protected $table = 'hr_requests_types';
    protected $casts = [
        "start_from" => "integer",
        "end_date" => "integer",
        "amount" => "integer",
        "to_hour" => "integer",
        "from_hour" => "integer",
    ];

    public function employees()
    {

        return $this->belongsToMany(Employee::class, 'hr_request_types_employees', 'request_type_id', 'employee_id');
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
