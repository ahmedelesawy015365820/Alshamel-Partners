<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateRpInstallmentPaymentPlanRequest;
use Modules\RecievablePayable\Http\Requests\EditRpInstallmentPaymentPlanRequest;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentPlanRepositoryInterface;
use Modules\RecievablePayable\Transformers\RpInstallmentPaymentPlanResource;

class RpInstallmentPaymentPlanController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpInstallmentPaymentPlanRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = cacheGet('RpInstallmentPaymentPlan_' . $id);
        if (!$model) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('RpInstallmentPaymentPlan_' . $id, $model);
            }
        }
        return responseJson(200, 'success', new RpInstallmentPaymentPlanResource($model));
    }

    public function index(Request $request)
    {
//        if (count($_GET) == 0) {
//            $models = cacheGet('RpInstallmentPaymentPlan');
//            if (!$models) {
//                $models = $this->modelInterface->all($request);
//                cachePut('RpInstallmentPaymentPlan', $models);
//            }
//        } else {
        $models = $this->modelInterface->all($request);
//        }

        return responseJson(200, 'success', RpInstallmentPaymentPlanResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateRpInstallmentPaymentPlanRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditRpInstallmentPaymentPlanRequest $request, $id)
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

    // public function destroy($id)
    // {
    //     $model = $this->modelInterface->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }
    //     $this->modelInterface->delete($id);

    //     return responseJson(200, 'success');
    // }


    public function destroy($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $relationsWithChildren = $model->hasChildren();

        if (!empty($relationsWithChildren)) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
            }
            return responseJson(400, $errorMessages);
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }



    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }
}
