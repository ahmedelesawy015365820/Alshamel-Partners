<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class SalesmenPlan extends Model
{
    use HasFactory, LogTrait;

    protected $table = "general_salesmen_plans";

    protected $fillable = [
        'name',
        'name_e',
        'salesmen_plans_source_id',
        'restart_period_id',
        'company_id',
    ];

    public function scopeData($query)
    {
        return $query->select(
            'name',
            'name_e',
            'salesmen_plans_source_id',
            'restart_period_id',
        )->with(['salesmenPlansSource:id,name,name_e','restartPeriod:id,name,name_e']);
    }

    public function salesmenPlansSource()
    {
        return $this->belongsTo(SalesmenPlansSource::class);
    }

    public function restartPeriod()
    {
        return $this->belongsTo(RestartPeriod::class);
    }

    public function details()
    {
        return $this->hasMany(SalesmenPlansDetail::class, 'plan_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'general_salesmen_plans_details_assignment', 'plan_id', 'employee_id');
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
            ->useLogName('SalesmenPlan')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
