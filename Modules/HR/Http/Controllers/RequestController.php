<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\HR\Entities\Request as EntitiesRequest;
use Modules\HR\Http\Requests\RequestRequest;
use Modules\HR\Transformers\RequestResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RequestController extends Controller
{
    public function __construct(private EntitiesRequest $model, private User $userModel, private Employee $employeeModel, private Media $media)
    {
        $this->model = $model;
        $this->userModel = $userModel;
        $this->employeeModel = $employeeModel;
        $this->media = $media;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        $from_id = $request['from_id'];
        $to_id = $request['to_id'];

        if ($from_id && $to_id) {
            $models->where('id', '>=', $from_id)
                ->where('id', '<=', $to_id);
        }

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }

        // if ($request->header('type') == 'user') {

        //     $models->where('employee_id', auth()->user()->employee_id);
        // }

        // if ($request->header('type') == 'user') {

        //     $requestTypeIds = RequestTypeEmployee::where('employee_id', auth()->user()->employee_id)->pluck('request_type_id');
        //     $models->whereIn('request_types_id', $requestTypeIds);
        // }

        if ($request->header('type') == 'user') {
            $employeeId = auth()->user()->employee_id;

            $models->where(function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)->orWhereRelation('employee', 'manager_id', $employeeId)
                    ->orWhereIn('request_types_id', function ($subQuery) use ($employeeId) {
                        $subQuery->select('request_type_id')
                            ->from('hr_request_types_employees')
                            ->where('employee_id', $employeeId);
                    });
            });
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        $responseData = [
            'message' => 'success',
            'data' => RequestResource::collection($models['data']),
            'pagination' => $models['paginate'] ? getPaginates($models['data']) : null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function getRequestSearch(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        $from_id = isset($request['data'][0]['from_id']) ? $request['data'][0]['from_id'] : null;
        $to_id = isset($request['data'][0]['to_id']) ? $request['data'][0]['to_id'] : null;

        if ($from_id && $to_id) {
            $models->where('id', '>=', $from_id)
                ->where('id', '<=', $to_id);
        }

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }
        if ($request->header('type') == 'user') {
            $employeeId = auth()->user()->employee_id;

            $models->where(function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                    ->orWhereIn('request_types_id', function ($subQuery) use ($employeeId) {
                        $subQuery->select('request_type_id')
                            ->from('hr_request_types_employees')
                            ->where('employee_id', $employeeId);
                    });
            });
        }

        // $from_date = $request->from_date;
        // $to_date = $request->to_date;

        $from_date = isset($request['data'][0]['from_date']) ? $request['data'][0]['from_date'] : null;
        $to_date = isset($request['data'][0]['to_date']) ? $request['data'][0]['to_date'] : null;

        if ($from_date && $to_date) {
            $models->whereBetween('updated_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        } elseif ($from_date) {
            $models->where('updated_at', '>=', $from_date . ' 00:00:00');
        } elseif ($to_date) {
            $models->where('updated_at', '<=', $to_date . ' 23:59:59');
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        $responseData = [
            'message' => 'success',
            'data' => RequestResource::collection($models['data']),
            'pagination' => $models['paginate'] ? getPaginates($models['data']) : null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function postRequestSearch(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        $from_id = isset($request['data'][0]['from_id']) ? $request['data'][0]['from_id'] : null;
        $to_id = isset($request['data'][0]['to_id']) ? $request['data'][0]['to_id'] : null;

        if ($from_id && $to_id) {
            $models->where('id', '>=', $from_id)
                ->where('id', '<=', $to_id);
        }

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }
        if ($request->header('type') == 'user') {
            $employeeId = auth()->user()->employee_id;

            $models->where(function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                    ->orWhereIn('request_types_id', function ($subQuery) use ($employeeId) {
                        $subQuery->select('request_type_id')
                            ->from('hr_request_types_employees')
                            ->where('employee_id', $employeeId);
                    });
            });
        }

        $from_date = isset($request['data'][0]['from_date']) ? $request['data'][0]['from_date'] : null;
        $to_date = isset($request['data'][0]['to_date']) ? $request['data'][0]['to_date'] : null;

        if ($from_date && $to_date) {
            $models->whereBetween('updated_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        } elseif ($from_date) {
            $models->where('updated_at', '>=', $from_date . ' 00:00:00');
        } elseif ($to_date) {
            $models->where('updated_at', '<=', $to_date . ' 23:59:59');
        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        $responseData = [
            'message' => 'success',
            'data' => RequestResource::collection($models['data']),
            'pagination' => $models['paginate'] ? getPaginates($models['data']) : null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function create(RequestRequest $request)
    {

        $requestData = $request->validated();

        $lastId = $this->model->max('id');
        $newId = $lastId ? $lastId + 1 : 1;

        $requestData['id'] = $newId;

        // $approved_by = isset($requestData['employee_id']) ? $requestData['employee_id'] : null;
        // if ($request->header('type') == 'user' && $approved_by != $requestData['approved_by']) {
        //     $requestData['approved_by'] = $approved_by;
        // } else {
        //     $requestData['approved_by'] = null;
        // }

        $model = $this->model->create($requestData);

        if ($request->media) {
            foreach ($request->media as $media) {
                $this->media::where('id', $media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        // if ($request->hasFile('media.0')) {
        //     $uploadedMedia = $request->file('media.0');
        //     $fileName = $uploadedMedia->store('media', 'public');

        //     $media = $this->media::create([
        //         'name' => $fileName,
        //         'model_id' => $model->id,
        //         'model_type' => get_class($this->model),
        //     ]);
        // }

        $responseData = [
            'message' => 'success',
            'data' => new RequestResource($model),
            'pagination' => null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function update($id, RequestRequest $request)
    {

        // return $request;
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($model->request_status_id != 1) {
            return responseJson(404, 'You can\'t Update this request the manager has received it');
        }
        // $data = $request->validated();

        $model->update($request->except(["media"]));

        if ($request->media && !$request->old_media) { // if there is new media and no old media
            $model->clearMediaCollection('media');
            foreach ($request->media as $media) {
                uploadImage($media, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if ($request->old_media && !$request->media) { // if there is old media and no new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
        }

        if ($request->old_media && $request->media) { // if there is old media and new media
            $model->media->whereNotIn('id', $request->old_media)->each(function (Media $media) {
                $media->delete();
            });
            foreach ($request->media as $image) {
                uploadImage($image, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (!$request->old_media && !$request->media) { // if this is no old media and new media
            $model->clearMediaCollection('media');
        }

        // if ($request->hasFile('media.0')) {
        //     $this->media::where('model_id', $model->id)->delete();
        //     $uploadedMedia = $request->file('media.0');
        //     $fileName = $uploadedMedia->store('media', 'public');

        //     $media = $this->media::create([
        //         'name' => $fileName,
        //         'model_id' => $model->id,
        //         'model_type' => get_class($this->model),
        //     ]);
        // }

        $model->refresh();
        $responseData = [
            'message' => 'updated',
            'data' => new RequestResource($model),
            'pagination' => null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($model->request_status_id != 1) {
            return responseJson(404, 'You can\'t delete this request the manager has received it');
        }

        if ($model instanceof \Spatie\MediaLibrary\HasMedia ) {
            $model->clearMediaCollection();
        }
        $model->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {
        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }

        $statuses = [];
        foreach ($models as $model) {
            if ($model->request_status_id != 1) {
                $statuses[] = ['id' => $model->id, 'status' => 'You can\'t delete this request; the manager has received it'];
            } else {
                $model->delete();
                $statuses[] = ['id' => $model->id, 'status' => 'deleted'];
            }
        }

        return responseJson(200, 'delete completed', $statuses);
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function updateManager(Request $request)
    {
        $id = $request->input('data')['id'];

        if (!$id) {
            return response()->json(['status' => 3], 400);
        }

//        $not_from_date = !isset($request['data']['approved_from_date']) && isset($request['data']['approved_to_date'])?
//            '|after_or_equal:data.approved_date': '';
//        $both_date = isset($request['data']['approved_from_date']) && isset($request['data']['approved_to_date'])?
//            '|after_or_equal:data.approved_from_date':'';

        $validatedData = $request->validate([
            // 'data.id' => 'required|exists:hr_statuses,id',
            'data.approved_from_date' => isset($request['data']['approved_from_date']) ? 'nullable|date|after_or_equal:data.approved_date' : 'nullable',
            'data.approved_to_date' => 'nullable|date',
            'data.approved_from_hour' => 'nullable',
            'data.approved_to_hour' => 'nullable' . isset($request['data']['approved_from_hour']) && isset($request['data']['approved_to_hour']) ? '|after:data.approved_from_hour' : '',
            'data.approved_amount' => 'nullable|numeric',
            'data.approved_date' => 'nullable|date',
            'data.request_status_id' => 'nullable|integer',
        ]);

        $requestModel = $this->model->find($id);

        if (!$requestModel) {
            return response()->json(['status' => 2], 404);
        }
        $approved_by = $request->header('type') == 'user' ? auth()->user()->employee_id : null;
        // Update
        $requestModel->update([
            'approved_from_date' => $validatedData['data']['approved_from_date'],
            'approved_to_date' => $validatedData['data']['approved_to_date'],
            'approved_from_hour' => $validatedData['data']['approved_from_hour'],
            'approved_to_hour' => $validatedData['data']['approved_to_hour'],
            'approved_amount' => $validatedData['data']['approved_amount'],
            'approved_date' => $validatedData['data']['approved_date'],
            'request_status_id' => $validatedData['data']['request_status_id'],
            'approved_by' => $approved_by,
        ]);

        $responseData = [
            'status' => 0,
            'message' => 'success',
            'data' => new RequestResource($requestModel),
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function getEmployeeLogin()
    {

        $employee = $this->employeeModel->where('id', auth()->user()->employee_id)->first();

        if (!$employee) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $responseData = [
            'message' => 'success',
            'data' => new EmployeeResource($employee),
            'pagination' => null,
        ];

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

}
