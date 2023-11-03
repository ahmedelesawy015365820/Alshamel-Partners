<?php

namespace App\Http\Controllers\RoleScreenHotfield;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleScreenHotfield\RoleScreenHotfieldRequest;
use App\Http\Requests\RoleScreenHotfield\UpdateRoleScreenHotfieldRequest;
use App\Http\Resources\RoleScreenHotfield\RoleScreenHotfieldResource;
use App\Http\Resources\ScreenSetting\ScreenSettingResource;
use App\Repositories\RoleScreenHotfield\RoleScreenHotfieldRepositoryInterface;
use Illuminate\Http\Request;

class RoleScreenHotfieldController extends Controller
{

    protected $repository;
    protected $resource = RoleScreenHotfieldResource::class;

    public function __construct(RoleScreenHotfieldRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request)
    {
        // if (count($_GET) == 0) {
        //     $models = cacheGet('RoleScreenHotfields');

        //     if (!$models) {
        //         $models = $this->repository->getAllRoleScreenHotfields($request);
        //         cachePut('RoleScreenHotfields', $models);
        //     }
        // } else {

        //     $models = $this->repository->getAllRoleScreenHotfields($request);
        // }

        $models = $this->repository->getAllRoleScreenHotfields($request);
        return responseJson(200, 'success', RoleScreenHotfieldResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = cacheGet('RoleScreenHotfields_' . $id);

        if (!$model) {
            $model = $this->repository->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('RoleScreenHotfields_' . $id, $model);
            }
        }
        return responseJson(200, __('Done'), new RoleScreenHotfieldResource($model));

    }

    public function create(RoleScreenHotfieldRequest $request)
    {

        return responseJson(200, __('Done'), $this->repository->create($request->validated()));

    }

    public function update(RoleScreenHotfieldRequest $request, $id)
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
