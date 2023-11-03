<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendantRequest;
use App\Http\Resources\AttendantResource;
use App\Repositories\Attendant\AttendantInterface;
use Illuminate\Http\Request;

class AttendantController extends Controller
{
    public function __construct(private AttendantInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new AttendantResource($model));
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', AttendantResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(AttendantRequest $request)
    {

        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(AttendantRequest $request, $id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->have_children) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }




}
