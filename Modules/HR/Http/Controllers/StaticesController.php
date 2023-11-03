<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Depertment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\Request;
use Modules\HR\Entities\RequestType;

class StaticesController extends Controller
{
    public function all()
    {
        $data = [];
        //الادارات
        $data['departmentsCount'] = Depertment::count();

        // الموظفين
        $data['employeesCount'] = Employee::count();

        // الوظفين لكل اداره
        $departments = Depertment::get();

        $data['employeeWithDepartmentCount'] = [];

        foreach ($departments as $department) {
            $employee_with_department_count = Employee::where('department_id', $department->id)->count();

            $percentage = $data['employeesCount'] != 0 ? round(($employee_with_department_count / $data['employeesCount']) * 100, 2) : 0;

            $data['employeeWithDepartmentCount'][] = [
                'name' => $department->name,
                'name_e' => $department->name_e,
                'count' => $employee_with_department_count,
                'percentage' => $percentage,
            ];

        }

        //عدد المستخدمين

        $data['usersCount'] = User::count();


        // انواع الطلبات
        $data['requestTypesCount'] = RequestType::count();

        //الطلبات

        $data['requestsCount'] = Request::count();

        // الطلبات لكل نوع طلب
        $requestTypes = RequestType::get();

        $data['requests_for_request_types'] = [];

        foreach ($requestTypes as $requestType) {

            $requests_for_request_types_count = Request::where('request_types_id', $requestType->id)->count();


            $percentage = $data['requestsCount'] != 0 ? round(($requests_for_request_types_count / $data['requestsCount']) * 100, 2) : 0;

            $data['requests_for_request_types'][] = [
                'name' => $requestType->name,
                'name_e' => $requestType->name_e,
                'count' => $requests_for_request_types_count,
                'percentage' => $percentage,
            ];

        }

        $response = [
            'message' => 'success',
            'data' => $data,
        ];

        return response()->json($response);
    }
}

