<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class PeriodicMaintenance extends Model
{
    use HasFactory, LogTrait;

    protected $table = "general_periodic_maintenances";

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            'date',
            'restart_period_id',
            'department_id',
            'task_id',
            'is_active',
        )->with('task:id,task_title', 'department:id,name,name_e', 'restartPeriod:id,name,name_e');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }

    public function restartPeriod()
    {
        return $this->belongsTo(RestartPeriod::class, 'restart_period_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Periodic Maintenance')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
