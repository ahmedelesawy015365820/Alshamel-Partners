<?php

namespace Modules\RecievablePayable\Http\Controllers;


use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateRpInstallmentStatusRequest;
use Modules\RecievablePayable\Http\Requests\CreateRpMainContactGroupRequest;
use Modules\RecievablePayable\Http\Requests\CreateRpScreenSubContactGroupRequest;
use Modules\RecievablePayable\Http\Requests\CreateRpSubContactGroupRequest;
use Modules\RecievablePayable\Http\Requests\EditRpInstallmentStatusRequest;
use Modules\RecievablePayable\Http\Requests\EditRpMainContactGroupRequest;
use Modules\RecievablePayable\Http\Requests\EditRpScreenSubContactGroupRequest;
use Modules\RecievablePayable\Http\Requests\EditRpSubContactGroupRequest;
use Modules\RecievablePayable\Repositories\RpInstallmentStatusRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpMainContactGroupRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpScreenSubContactGroupRepositoryInterface;
use Modules\RecievablePayable\Repositories\RpSubContactGroupRepositoryInterface;
use Modules\RecievablePayable\Transformers\RpInstallmentStatusResource;
use Modules\RecievablePayable\Transformers\RpMainContactGroupResource;
use Modules\RecievablePayable\Transformers\RpScreenSubContactGroupResource;
use Modules\RecievablePayable\Transformers\RpSubContactGroupResource;

class RpScreenSubContactGroupController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpScreenSubContactGroupRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = cacheGet('RpScreenSubContactGroup_' . $id);
        if (!$model) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('RpScreenSubContactGroup_' . $id, $model);
            }
        }
        return responseJson(200, 'success', new RpScreenSubContactGroupResource($model));
    }

    public function index(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('RpScreenSubContactGroup');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('RpScreenSubContactGroup', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', RpScreenSubContactGroupResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateRpScreenSubContactGroupRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditRpScreenSubContactGroupRequest $request, $id)
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
