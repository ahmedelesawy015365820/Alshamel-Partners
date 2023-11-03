<?php

namespace App\Http\Controllers\InternalSalesman;

use App\Http\Controllers\Controller;
use App\Http\Requests\InternalSalesman\StoreInternalSalesmanRequest;
use App\Http\Requests\InternalSalesman\UpdateInternalSalesmanRequest;
use App\Http\Resources\InternalSalesman\InternalSalesmanResource;
use App\Http\Resources\ScreenSetting\ScreenSettingResource;
use App\Repositories\InternalSalesman\InternalSalesmanRepositoryInterface;
use Illuminate\Http\Request;

class InternalSalesmanController extends Controller
{
    protected $repository;
    protected $resource = InternalSalesmanResource::class;

    public function __construct(InternalSalesmanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function all(Request $request)
    {

        $models = $this->repository->getAllInternalSalesmen($request);
        return responseJson(200, 'success', InternalSalesmanResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    public function find($id)
    {

        $model = $this->repository->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, __('Done'), new InternalSalesmanResource($model));

    }

    public function create(StoreInternalSalesmanRequest $request)
    {
        return responseJson(200, __('Done'), $this->repository->create($request->validated()));

    }

    public function update(UpdateInternalSalesmanRequest $request, $id)
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
