<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class EmployeesTimeTablesHeader extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'hr_employees_timetables_header';

    protected $guarded = ['id'];
    public function scopeData($query)
    {
        return $query->select('id', 'timetables_header_id', 'start_from')
            ->with('timetablesHeader:id,name,name_e');
    }

    public function timetablesHeader()
    {
        return $this->belongsTo(TimeTablesHeader::class, 'timetables_header_id', 'id');
    }

    public function employeesTimeTablesDetails()
    {
        return $this->hasMany(EmployeesTimeTablesDetail::class, 'employees_timetables_header_id', 'id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->employeesTimeTablesDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Employees Time Tables Details',
                'count' => $this->employeesTimeTablesDetails()->count(),
                'ids' => $this->employeesTimeTablesDetails()->pluck('id')->toArray()
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Employees Time Tables Header')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
