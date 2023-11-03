<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Http\Requests\CategoriesItemRequest;
use Modules\RealEstate\Repositories\RlstCategoriesItem\RlstCategoriesItemInterface;
use Modules\RealEstate\Transformers\RlstCategoriesItemResource;

class RlstCategoriesItemController extends Controller
{

    public function __construct(private RlstCategoriesItemInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', RlstCategoriesItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new RlstCategoriesItemResource($model));
    }

    public function create(CategoriesItemRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return responseJson(200, 'success', new RlstCategoriesItemResource($model));
    }

    // public function update(CategoriesItemRequest $request, $id)
    // {
    //     $model = $this->modelInterface->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }
    //     $model = $this->modelInterface->update($request, $id);

    //     return responseJson(200, 'success', new RlstCategoriesItemResource($model));
    // }


    public function delete($category_item_id,$item_id)
    {
        $model = $this->modelInterface->checkDelete($category_item_id,$item_id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($category_item_id,$item_id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }
    // public function bulkDelete(Request $request)
    // {
    //     foreach ($request->ids as $id) {
    //         $model = $this->modelInterface->find($id);
    //         $arr = [];
    //         if ($model->have_children) {
    //             $arr[] = $id;
    //             continue;
    //         }
    //         $this->modelInterface->delete($id);
    //     }
    //     if (count($arr) > 0) {
    //         return responseJson(400, __('some items has relation cant delete'));
    //     }
    //     return responseJson(200, __('Done'));
    // }

}
