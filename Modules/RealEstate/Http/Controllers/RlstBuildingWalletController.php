<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstBuildingWallet;
use Modules\RealEstate\Http\Requests\RlstBuildingWalletRequest;
use Modules\RealEstate\Transformers\RlstBuildingWalletResource;

class RlstBuildingWalletController extends Controller
{

    public function __construct(private RlstBuildingWallet $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstBuildingWalletResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstBuildingWalletResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(RlstBuildingWalletRequest $request)
    {
       //return $request['building-wallet'][0];
        
        foreach ($request['building-wallet'] as $building_wallet) {
            $model = $this->model->create($building_wallet);
        }
        $model->refresh();
        return responseJson(200, 'success');
    }

    public function update($id, RlstBuildingWalletRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        
        if ($request['building-wallet']) {
            foreach ($request['building-wallet'] as $building_wallet) {
               $model->update($building_wallet);
            }
        }
        return responseJson(200, 'updated', new RlstBuildingWalletResource($model));
    }


    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        
        $model->deleted_at = now();
        $model->save();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->deleted_at = now();
            $model->save();
        });
        return responseJson(200, 'deleted');
    }
}
