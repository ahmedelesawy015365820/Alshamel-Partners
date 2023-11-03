<?php

namespace App\Models;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;

class InternalSalesman extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $table = 'general_internal_salesman';

    protected $guarded = ["id"];

    public function scopeData($query)
    {
        return $query->select("id", "employee_id", "is_active")
                    ->with('employee:id,name,name_e');
    }

    protected $casts = [
        'is_active' => 'App\Enums\IsActive',
    ];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->causer_id = auth()->user()->id ?? 0;
        $activity->causer_type = auth()->user()->role ?? "admin";
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return LogOptions::defaults()
            ->logAll()
            ->useLogName('InternalSalesman')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
