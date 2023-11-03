<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmergencyBalance;
use Modules\HR\Transformers\EmergencyBalanceResource;

class EmergencyBalanceController extends Controller
{
    public function __construct(private EmergencyBalance $model, private Employee $modelEmployee)
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

        return responseJson(200, 'success', EmergencyBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getEmployee(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $employee = Employee::find($data['data']['employee_id']);

        if (!$employee) {

            return responseJson(400, 'Employee not found');
        }

        $responseData = $data;
        $responseData['employee'] = new EmergencyBalanceResource($employee);

        return responseJson(200, $responseData);
    }

    public function store(Request $request)
    {

        $existEmployee = Employee::where('id', $request->employee_id)->first();

        if ($existEmployee) {

            $employee = EmergencyBalance::create([

                'employee_id' => $request->employee_id,
                'date' => $request->YYYYMM,
                'days' => $request->days,

            ]);
            return responseJson(200, 'created', new EmergencyBalanceResource($employee));
        }
        return responseJson(400, 'failed', 'There is no Employee with this ID');

    }

    public function processJsonData(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $messages = [];

        foreach ($data['data'] as $item) {
            try {
                $employee_id = $item['id'] ?? null;
                $YYYYMM = $item['YYYYMM'];

                $existingEmployeeId = $this->model
                    ->where('employee_id', $employee_id)
                    ->where('date', $YYYYMM)
                    ->first();

                $existEmployee = $this->modelEmployee->where('id', $employee_id)->first();

                switch ($item['op']) {
                    case 'ADD':
                        $existingRecord = $this->model
                            ->where('employee_id', $employee_id)
                            ->where('date', $YYYYMM)
                            ->where('days', $item['days'])
                            ->first();

                        if ($existingRecord) {
                            $existingRecord->delete();
                            $this->model->insert([
                                'id' => $item['id'],
                                'employee_id' => $employee_id,
                                'date' => $YYYYMM,
                                'days' => $item['days'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $messages[] = ['Employee_id' => $employee_id, 'status' => 0];
                            break;

                        }

                        if ($existingEmployeeId) {
                            $messages[] = ['Employee_id' => $employee_id, 'status' => 3];
                            break;
                        }

                        if ($existEmployee) {

                            $this->model->insert([
                                'id' => $item['id'],
                                'employee_id' => $employee_id,
                                'date' => $YYYYMM,
                                'days' => $item['days'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $messages[] = ['id' => $employee_id, 'status' => 0];
                            break;
                        } else {
                            $messages[] = ['id' => $employee_id, 'status' => 4];
                            break;
                        }
                    case 'UPD':
                        $model = $this->model
                            ->where('id', $item['id'], )
                            ->first();

                        if ($model) {
                            if ($existingEmployeeId) {
                                $messages[] = ['Employee_id' => $employee_id, 'status' => 3];
                                break;
                            }
                            if ($existEmployee) {
                                try {
                                    $model->update([
                                        'id' => $item['id'],
                                        'employee_id' => $employee_id,
                                        'date' => $YYYYMM,
                                        'days' => $item['days'],
                                        'updated_at' => now(),
                                    ]);

                                    $messages[] = ['id' => $employee_id, 'status' => 0];
                                } catch (\Exception $e) {
                                    $messages[] = ['id' => $employee_id, 'status' => 10];
                                }} else {
                                $messages[] = ['id' => $employee_id, 'status' => 4];
                                break;
                            }
                        } else {
                            $messages[] = ['id' => $employee_id, 'status' => 5];
                            break;
                        }
                        break;
                    case 'DEL':
                        $model = $this->model->where('id', $item['id'])->first();
                        if ($model) {
                            $model->delete();
                            $messages[] = ['id' => $employee_id, 'status' => 0];
                        } else {
                            $messages[] = ['id' => $employee_id, 'status' => 2];
                        }
                        break;
                }
            } catch (\Exception $e) {
                $messages[] = ['employee_id' => $item['employee_id'], 'status' => $e->getMessage()];
            }
        }

        return response()->json($messages);
    }

}
