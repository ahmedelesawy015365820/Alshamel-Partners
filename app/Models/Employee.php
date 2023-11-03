<?php

namespace App\Models;

use App\Models\Depertment;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Task;
use Modules\HR\Entities\EmployeesTimeTablesDetail;
use Modules\HR\Entities\Fingerprint;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Entities\RequestType;

class Employee extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_employees';
    protected $casts = ["manage_others" => "integer"];
    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id',
                'manager_id',
                'name',
                'name_e',
                'is_salesman',
                'customer_handel',
                'department_id',
                'code_country',
                'salesman_type_id',
                'for_all_customer',
                'mobile',
                'email',
                'whatsapp',
                'sms',
                'att_code',
                // 'company_id',
                'job_id',
                'manage_others',
                'branch_id',
            )
            ->with(
                'jobTitle:id,name,name_e',
                'branch:id,name,name_e',
                'manager:id,name,name_e',
                'salesmanType:id,name,name_e',
                'department:id,name,name_e',
                'plans:id,name,name_e',
                'managersData'
            );
    }

    public function scopeEmployeesTimeTablesDetailsData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'att_code',
                'department_id',
                'branch_id',
            )
            ->with(
                'branch:id,name,name_e',
                'department:id,name,name_e',
            );
    }

    public function scopeEmployeesAttendanceDepartureReportData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'att_code',
                'department_id',
                'branch_id',
            )
            ->with(
                'fingerprints:id,att_code,vdate,att_type,machine_id',
            );
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'job_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function internalSalesman()
    {
        return $this->hasOne(InternalSalesman::class);
    }

    public function department()
    {
        return $this->belongsTo(Depertment::class, 'department_id');
    }



//    public function tasksOwner()
//    {
//        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'owner_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
//    }
//
//    public function tasksSupervisor()
//    {
//        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'supervisor_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
//    }

    public function tasksnNotification()
    {
        return $this->belongsToMany(Task::class, 'boards_rent_employee_tasks', 'task_id', 'notification_id')->withPivot('owner_id', 'supervisor_id', 'notification_id');
    }

    public function salesmanType()
    {
        return $this->belongsTo(SalesmenType::class, 'salesman_type_id');
    }

    public function plans()
    {

        return $this->belongsToMany(SalesmenPlan::class, 'general_salesmen_plans_details_assignment', 'employee_id', 'plan_id');
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'general_documents_approve_personal', 'employee_id', 'document_id', 'id', 'id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'employee_id');

    }
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function children()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function request_types()
    {

        return $this->belongsToMany(RequestType::class, 'hr_request_types_employees', 'employee_id', 'request_type_id');
    }

    public function managers()
    {
        return $this->belongsToMany(Employee::class, 'general_employee_managers', 'employee_id', 'manager_id');
    }

    public function EmployeesTimeTablesDetails()
    {
        return $this->hasMany(EmployeesTimeTablesDetail::class, 'employee_id');
    }

    public function fingerprints()
    {
        return $this->hasMany(Fingerprint::class, 'att_code');
    }

    public function managersData()
    {
        return $this->hasMany(GeneralEmployeeManager::class, 'employee_id')
            ->select('employee_id', 'manager_id')
            ->leftJoin('general_employees', 'general_employee_managers.manager_id', '=', 'general_employees.id')
            ->select('general_employee_managers.employee_id', 'general_employee_managers.manager_id', 'general_employees.name AS manager_name', 'general_employees.name_e AS manager_name_e');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];



         if ($this->children()->count() > 0) {
             $relationsWithChildren[] = [
                 'relation' => 'Employees',
                 'count' => $this->children()->count(),
                 'ids' => $this->children()->pluck('id')->toArray(),
             ];
         }
          if ($this->request_types()->count() > 0) {
              $relationsWithChildren[] = [
                  'relation' => 'request_types',
                  'count' => $this->request_types()->count(),
                  'ids' => $this->request_types()->pluck('id')->toArray(),
              ];
          }
//            if ($this->plans()->count() > 0) {
//                $relationsWithChildren[] = [
//                    'relation' => 'plans',
//                    'count' => $this->plans()->count(),
//                    'ids' => $this->plans()->pluck('id')->toArray() ?? null,
//                ];
//            }

        if ($this->documents()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documents',
                'count' => $this->documents()->count(),
                'ids' => $this->documents()->pluck('id')->toArray(),
            ];
        }

//       if ($this->tasksOwner()->count() > 0) {
//           $relationsWithChildren[] = [
//               'relation' => 'tasksOwner',
//               'count' => $this->tasksOwner()->count(),
//               'ids' => $this->tasksOwner()->pluck('id')->toArray(),
//           ];
//       }
//       if ($this->tasksSupervisor()->count() > 0) {
//           $relationsWithChildren[] = [
//               'relation' => 'tasksSupervisor',
//               'count' => $this->tasksSupervisor()->count(),
//               'ids' => $this->tasksSupervisor()->pluck('id')->toArray(),
//           ];
//       }
        if ($this->managers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'managers',
                'count' => $this->managers()->count(),
                'ids' => $this->managers()->pluck('id')->toArray(),
            ];
        }

         if ($this->EmployeesTimeTablesDetails()->count() > 0) {
             $relationsWithChildren[] = [
                 'relation' => 'EmployeesTimeTablesDetails',
                 'count' => $this->EmployeesTimeTablesDetails()->count(),
                 'ids' => $this->EmployeesTimeTablesDetails()->pluck('id')->toArray(),
             ];
         }

          if ($this->fingerprints()->count() > 0) {
              $relationsWithChildren[] = [
                  'relation' => 'fingerprints',
                  'count' => $this->fingerprints()->count(),
                  'ids' => $this->fingerprints()->pluck('id')->toArray(),
              ];
          }

          if ($this->managersData()->count() > 0) {
              $relationsWithChildren[] = [
                  'relation' => 'managersData',
                  'count' => $this->managersData()->count(),
                  'ids' => $this->managersData()->pluck('id')->toArray(),
              ];
          }

        if ($this->documentHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeader',
                'count' => $this->documentHeader()->count(),
                'ids' => $this->documentHeader()->pluck('id')->toArray(),
            ];
        }
        if ($this->user()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'user',
                'count' => $this->user()->count(),
                'ids' => $this->user()->pluck('id')->toArray(),
            ];
        }

        if ($this->internalSalesman()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'internalSalesman',
                'count' => $this->internalSalesman()->count(),
                'ids' => $this->internalSalesman()->pluck('id')->toArray(),
            ];
        }





        return $relationsWithChildren;
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     $user = auth()->user()->id ?? "system";

    //     return \Spatie\Activitylog\LogOptions::defaults()
    //         ->logAll()
    //         ->useLogName('Employee')
    //         ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    // }
}
