<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class EmployeesTimeTablesDetail extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'hr_employees_timetables_details';

    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query->select('id', 'employees_timetables_header_id', 'employee_id')
            ->with('timetablesHeader:id,name,name_e','employee:id,name,name_e');
    }


    public function employeeTimetablesHeader()
    {
        return $this->belongsTo(EmployeesTimeTablesHeader::class, 'employees_timetables_header_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
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
