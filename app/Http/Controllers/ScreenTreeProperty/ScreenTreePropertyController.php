<?php

namespace App\Http\Controllers\ScreenTreeProperty;

use App\Http\Requests\ScreenTreeProperty\ScreenTreePropertyRequest;
use App\Http\Requests\ScreenTreeProperty\EditScreenTreePropertyRequest;
use App\Http\Resources\ScreenTreeProperty\ScreenTreePropertyResource;
use App\Repositories\ScreenTreeProperty\ScreenTreePropertyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ScreenTreePropertyController extends Controller
{
    private $modelInterface;
    public function __construct(ScreenTreePropertyRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new ScreenTreePropertyResource($model));
    }

    public function index(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', ScreenTreePropertyResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(ScreenTreePropertyRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(ScreenTreePropertyRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function setting(Request $request)
    {
        $model = $this->modelInterface->setting($request);

        return responseJson(200, 'success');
    }

    public function getSetting($user_id, $screen_id)
    {
        $model = $this->modelInterface->getSetting($user_id, $screen_id);
        return responseJson(200, 'success', new \App\Http\Resources\ScreenSetting\ScreenSettingResource($model));
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

    public function destroy($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }
}
