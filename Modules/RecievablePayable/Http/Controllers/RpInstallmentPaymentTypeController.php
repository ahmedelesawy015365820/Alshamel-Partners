<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Traits\BulkDeleteTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateRpInstallmentPaymentTypeRequest;
use Modules\RecievablePayable\Http\Requests\EditRpInstallmentPaymentTypeRequest;
use Modules\RecievablePayable\Repositories\RpInstallmentPaymentTypeRepositoryInterface;
use Modules\RecievablePayable\Transformers\RpInstallmentPaymentTypeResource;

class RpInstallmentPaymentTypeController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpInstallmentPaymentTypeRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {

        $model = $this->modelInterface->find($id);
        return responseJson(200, 'success', new RpInstallmentPaymentTypeResource($model));
    }

    public function index(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', RpInstallmentPaymentTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateRpInstallmentPaymentTypeRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditRpInstallmentPaymentTypeRequest $request, $id)
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


    // public function bulkDelete(Request $request)
    // {
    //     $itemsWithRelations = [];

    //     foreach ($request->ids as $id) {
    //         $model = $this->modelInterface->find($id);

    //         $relationsWithChildren = $model->hasChildren();
    //         if (!empty($relationsWithChildren)) {
    //             $itemsWithRelations[] = [
    //                 'id' => $id,
    //                 'relations' => $relationsWithChildren,
    //             ];
    //             continue;
    //         }

    //         $this->modelInterface->delete($id);
    //     }

    //     if (count($itemsWithRelations) > 0) {
    //         $errorMessages = [];
    //         foreach ($itemsWithRelations as $item) {
    //             $itemId = $item['id'];
    //             $relations = $item['relations'];

    //             $relationErrorMessages = [];
    //             foreach ($relations as $relation) {
    //                 $relationName = $this->getRelationDisplayName($relation['relation']);
    //                 $childCount = $relation['count'];
    //                 $childIds = implode(', ', $relation['ids']);
    //                 $relationErrorMessages[] = "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
    //             }

    //             $errorMessages[] = implode(' ', $relationErrorMessages);
    //         }

    //         return responseJson(400, $errorMessages);
    //     }

    //     return responseJson(200, __('Done'));
    // }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }
}
