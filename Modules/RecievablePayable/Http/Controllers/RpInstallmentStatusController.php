<?php

namespace Modules\RecievablePayable\Http\Controllers;


use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateRpInstallmentStatusRequest;
use Modules\RecievablePayable\Http\Requests\EditRpInstallmentStatusRequest;
use Modules\RecievablePayable\Repositories\RpInstallmentStatusRepositoryInterface;
use Modules\RecievablePayable\Transformers\RpInstallmentStatusResource;

class RpInstallmentStatusController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpInstallmentStatusRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = cacheGet('RpInstallmentStatus_' . $id);
        if (!$model) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('RpInstallmentStatus_' . $id, $model);
            }
        }
        return responseJson(200, 'success', new RpInstallmentStatusResource($model));
    }

    public function index(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('RpInstallmentStatus');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('RpInstallmentStatus', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', RpInstallmentStatusResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateRpInstallmentStatusRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditRpInstallmentStatusRequest $request, $id)
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


}
