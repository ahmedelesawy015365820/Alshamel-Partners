<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\VacationBalance;
use Modules\HR\Transformers\VacationBalanceResource;

class VacationBalanceController extends Controller
{

    public function __construct(private VacationBalance $model, private Employee $modelEmployee)
    {
        $this->model = $model;
        $this->modelEmployee = $modelEmployee;

    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }
        if ($request->header('type') == 'user') {
            $models->where('employee_id', auth()->user()->employee_id);
        }


        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', VacationBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getEmployee(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $employee = Employee::find($data['data']['id']);

        if (!$employee) {

            return responseJson(400, 'Employee not found');
        }

        $responseData = $data;
        $responseData['employee'] = new EmployeeResource($employee);

        return responseJson(200, $responseData);
    }

    public function store(Request $request)
    {

        $employee = VacationBalance::create([

            'employee_id' => $request->data['id'],
            'date' => $request->data['YYYYMM'],
            'days' => $request->data['days'],

        ]);

        return responseJson(200, 'created', new VacationBalanceResource($employee));
    }

    public function processJsonData(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $messages = [];

        foreach ($data['data'] as $item) {
            try {
                $id = $item['id'] ?? null;
                $YYYYMM = $item['YYYYMM'];

                $existingEmployeeId = $this->model
                    ->where('employee_id', $id)
                    ->where('date', $YYYYMM)
                    ->first();

                $existEmployee = $this->modelEmployee->where('id', $id)->first();

                switch ($item['op']) {
                    case 'ADD':
                        if ($existingEmployeeId) {
                            $messages[] = ['id' => $id, 'status' => 'this employee_id used with the same date'];
                            break;
                        }

                        if ($existEmployee) {

                            $this->model->insert([
                                'employee_id' => $id,
                                'date' => $YYYYMM,
                                'days' => $item['days'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $messages[] = ['id' => $id, 'status' => 'added successfully'];
                            break;
                        } else {
                            $messages[] = ['id' => $id, 'status' => 'this Employee dosen\'t exist in employees table'];
                            break;
                        }
                    case 'UPD':
                        $model = $this->model
                            ->where('employee_id', $id)
                            ->where('date', $YYYYMM)
                            ->first();

                        if ($model) {
                            if ($existEmployee) {
                                try {
                                    $model->update([
                                        'employee_id' => $id,
                                        'date' => $YYYYMM,
                                        'days' => $item['days'],
                                        'updated_at' => now(),
                                    ]);

                                    $messages[] = ['id' => $id, 'status' => 'updated successfully'];
                                } catch (\Exception $e) {
                                    $messages[] = ['id' => $id, 'status' => 'error updating record'];
                                }} else {
                                $messages[] = ['id' => $id, 'status' => 'this Employee dosen\'t exist in employees table'];
                                break;
                            }
                        } else {
                            $messages[] = ['id' => $id, 'status' => 'Can\'t updated you need to provide date and employee_id first'];
                                break;
                        }
                        break;
                    case 'DEL':
                        $model = $this->model->where('employee_id', $id)->where('date', $YYYYMM)->first();
                        if ($model) {
                            $model->delete();
                            $messages[] = ['id' => $id, 'status' => 'deleted successfully'];
                        } else {
                            $messages[] = ['id' => $id, 'status' => 'record not found'];
                        }
                        break;
                }
            } catch (\Exception $e) {
                $messages[] = ['id' => $item['id'], 'status' => $e->getMessage()];
            }
        }

        return response()->json($messages);
    }

}
