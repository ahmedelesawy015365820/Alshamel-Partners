<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateOpeningBalanceRequest;
use Modules\RecievablePayable\Http\Requests\EditOpeningBalanceRequest;
use Modules\RecievablePayable\Repositories\RpOpeningBalanceInterface;
use Modules\RecievablePayable\Transformers\OpeningBalanceResource;

class OpeningBalanceController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpOpeningBalanceInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new OpeningBalanceResource($model));
    }

    public function index(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', OpeningBalanceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateOpeningBalanceRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return $model;
        return responseJson(200, 'success', new OpeningBalanceResource($model));
    }

    public function update(EditOpeningBalanceRequest $request, $id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);
        return responseJson(200, 'success', new OpeningBalanceResource($model));
    }

    public function setting(Request $request)
    {
        $model = $this->modelInterface->setting($request);
        return responseJson(200, 'success');

    }

    public function getSetting($user_id, $screen_id)
    {
        $model = $this->modelInterface->getSetting($user_id, $screen_id);
        return responseJson(200, 'success', new \App\Http\Resources\ScreenSetting\ScreenSettingResource ($model));
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

//     public function destroy($id)
//     {
//         $model = $this->modelInterface->find($id);
//         if (!$model) {
//             return responseJson(404, __('message.data not found'));
//         }
// //        if ($model->hisChildren()){
// //            return responseJson(404, 'some items has relation cant delete');
// //        }
//          $this->modelInterface->delete($id);

//         return responseJson(200, 'success');
//     }

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
