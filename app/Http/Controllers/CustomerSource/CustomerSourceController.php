<?php

namespace App\Http\Controllers\CustomerSource;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCategory\CustomerCategoryRequest;
use App\Http\Requests\CustomerSource\CustomerSourceRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\CustomerCategory\CustomerCategoryResource;
use App\Http\Resources\CustomerSource\CustomerSourceResource;
use App\Http\Resources\CustomerSource\GetNameCustomerSourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSourceController extends Controller
{
    public function __construct(private \App\Repositories\CustomerSource\CustomerSourceRepository $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CustomerSourceResource($model));
    }


    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', CustomerSourceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(CustomerSourceRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        return responseJson(200, 'success', new CustomerSourceResource($model));
    }



    public function update(CustomerSourceRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(), $id);
        $model->refresh();
        return responseJson(200, 'success', new CustomerSourceResource($model));
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


    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }
        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {

        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function processJsonData(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        try {
            $messages = $this->modelInterface->processJsonData($data);
            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDropDown(Request $request)
    {

        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
