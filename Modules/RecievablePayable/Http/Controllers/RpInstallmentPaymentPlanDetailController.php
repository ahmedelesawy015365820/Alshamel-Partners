<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Traits\BulkDeleteTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentPlan;
use Modules\RecievablePayable\Http\Requests\CreateRpInstallmentPaymentPlanDetailRequest;
use Modules\RecievablePayable\Http\Requests\EditRpInstallmentPaymentPlanDetailRequest;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentPlanDetailRepositoryInterface;
use Modules\RecievablePayable\Transformers\RpInstallmentPaymentPlanDetailResource;
use Modules\RecievablePayable\Transformers\RpInstallmentPaymentPlanResource;

class RpInstallmentPaymentPlanDetailController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpInstallmentPaymentPlanDetailRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function allPlan(Request $request)
    {

      $models = $this->modelInterface->allPlan($request);

        return responseJson(200, 'success', RpInstallmentPaymentPlanResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function show($id)
    {
        $model = cacheGet('RpInstallmentPaymentPlanDetail_' . $id);
        if (!$model) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('RpInstallmentPaymentPlanDetail_' . $id, $model);
            }
        }
        return responseJson(200, 'success', new RpInstallmentPaymentPlanDetailResource($model));
    }

    public function index(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('RpInstallmentPaymentPlanDetail');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('RpInstallmentPaymentPlanDetail', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', RpInstallmentPaymentPlanDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateRpInstallmentPaymentPlanDetailRequest $request)
    {

        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditRpInstallmentPaymentPlanDetailRequest $request, $id)
    {

        $model = $this->modelInterface->findPlan($id);
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
        $model = $this->modelInterface->findPlan($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }
}
