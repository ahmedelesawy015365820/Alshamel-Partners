<?php

namespace App\Http\Controllers\WorkflowHotfield;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkflowHotfield\WorkflowHotfieldRequest;
use App\Http\Requests\WorkflowHotfield\UpdateWorkflowHotfieldRequest;
use App\Http\Resources\ScreenSetting\ScreenSettingResource;
use App\Http\Resources\WorkflowHotfield\WorkflowHotfieldResource;
use App\Repositories\WorkflowHotfield\WorkflowHotfieldRepositoryInterface;
use Illuminate\Http\Request;

class WorkflowHotfieldController extends Controller
{

    protected $repository;
    protected $resource = WorkflowHotfieldResource::class;

    public function __construct(WorkflowHotfieldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request)
    {
        // if (count($_GET) == 0) {
        //     $models = cacheGet('WorkflowHotfields');

        //     if (!$models) {
        //         $models = $this->repository->getAllWorkflowHotfields($request);
        //         cachePut('WorkflowHotfields', $models);
        //     }
        // } else {

        //     $models = $this->repository->getAllWorkflowHotfields($request);
        // }
        $models = $this->repository->getAllWorkflowHotfields($request);

        return responseJson(200, 'success', WorkflowHotfieldResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        // $model = cacheGet('WorkflowHotfields_' . $id);

        // if (!$model) {
        //     $model = $this->repository->find($id);
        //     if (!$model) {
        //         return responseJson(404, __('message.data not found'));
        //     } else {
        //         cachePut('WorkflowHotfields_' . $id, $model);
        //     }
        // }
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, __('Done'), new WorkflowHotfieldResource($model));

    }

    public function create(WorkflowHotfieldRequest $request)
    {

        return responseJson(200, __('Done'), $this->repository->create($request->validated()));

    }

    public function update(WorkflowHotfieldRequest $request, $id)
    {

        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->repository->update($request->validated(), $id);

        return responseJson(200, __('Done'));

    }

    public function delete($id)
    {

        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->repository->delete($id);
        return responseJson(200, __('Done'));

    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->repository->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function logs($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

    public function screenSetting(Request $request)
    {

        return responseJson(200, __('Done'), $this->repository->setting($request->all()));

    }

    public function getScreenSetting($user_id, $screen_id)
    {

        $screenSetting = $this->repository->getSetting($user_id, $screen_id);
        if (!$screenSetting) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, __('Done'), new ScreenSettingResource($screenSetting));

    }

}
