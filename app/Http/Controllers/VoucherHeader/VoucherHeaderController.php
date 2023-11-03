<?php

namespace App\Http\Controllers\VoucherHeader;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentHeader\DocumentHeaderRequest;
use App\Http\Requests\VoucherHeader\VoucherHeaderRequest;
use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use App\Http\Resources\VoucherHeader\VoucherHeaderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherHeaderController extends Controller
{
    public function __construct(private \App\Repositories\VoucherHeader\VoucherHeaderInterface$modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new VoucherHeaderResource($model));
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', VoucherHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(VoucherHeaderRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        $model->refresh();
        $serials = generalSerial($model);
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);
        return responseJson(200, 'success', new VoucherHeaderResource($model));
    }



    public function update(VoucherHeaderRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson( 404 , __('message.data not found'));
        }
        $model_Interface = $this->modelInterface->update($request->validated(),$id);
        $model->refresh();
        return responseJson(200, 'success', new VoucherHeaderResource($model));

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
//        if ($model->hasChildren()){
//            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
//        }

        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {

        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }



}
