<?php

namespace App\Models;

use App\Models\Depertment;
use App\Models\DepertmentTask;
use App\Models\Employee;
use App\Models\GeneralCustomer;
use App\Models\Status;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class Task extends Model implements HasMedia
{
    use HasFactory,  MediaTrait;

    protected $guarded = ['id'];


    protected $table = "general_tasks";

    public function scopeData($query)
    {
        return $query
            ->select(
                'id','contact_person','contact_phone','task_title','execution_date','execution_duration','customer_id',
                'execution_end_date','notification_date','start_time','end_time','department_task_id','employee_id',
                'customer_id','department_id','status_id','note','location_id','priority_id','is_closed','admin_note',
                'task_requirement','type','equipment_id'
            )
            ->with([
                'departmentTask:id,name,name_e','department:id,name,name_e','employee:id,name,name_e',
                'customer:id,name,name_e,contact_person,contact_phone','equipment:id,name,name_e',
                'status:id,name,name_e','location:id,name,name_e','priority:id,name,name_e'
            ]);
    }

    public function departmentTask()
    {
        return $this->belongsTo(DepertmentTask::class, 'department_task_id');
    }
    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function owners()
    {

        return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'owner_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    // public function supervisors()
    // {

    //     return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'supervisor_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    // }

    public function notifications()
    {

        return $this->belongsToMany(Employee::class, 'boards_rent_employee_tasks', 'task_id', 'notification_id', 'id', 'id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }


    public function logs()
    {
        return $this->hasMany(TaskLog::class, 'task_id');
    }



    public function supervisors()
    {

        return $this->belongsToMany(Employee::class, 'general_employee_task', 'task_id', 'employee_id', 'id', 'id')->wherePivot('type', 'supervisor');
    }


    public function attentions()
    {

        return $this->belongsToMany(Employee::class, 'general_employee_task', 'task_id', 'employee_id', 'id', 'id')->wherePivot('type', 'attention');
    }

    // scopes

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->has('date')) {
                $q->whereDate('date', $request->date);
            }

            if ($request->search && $request->columns) {
                foreach ($request->columns as $column) {
                    if (strpos($column, ".") > 0) {
                        $column = explode(".", $column);
                        $q->orWhereRelation($column[0], $column[1], 'like', '%' . $request->search . '%');
                        // $q->orWhereHas($column[0], function ($q) use ($column, $request) {
                        //     $q->where($column[1], 'like', '%' . $request->search . '%');
                        // });
                    } else {
                        $q->orWhere($column, 'like', '%' . $request->search . '%');
                    }
                }
            }

            if (request()->header('company-id')) {
                if (in_array('company_id', $this->fillable)) {
                    $q->where('company_id', request()->header('company-id'));
                }
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });
    }
}
