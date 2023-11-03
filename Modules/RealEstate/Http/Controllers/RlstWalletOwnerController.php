<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstWalletOwner;
use Modules\RealEstate\Http\Requests\RlstWalletOwnerRequest;
use Modules\RealEstate\Transformers\RlstWalletOwnerResource;
use Modules\RealEstate\Entities\RlstWallet;

class RlstWalletOwnerController extends Controller
{

    public function __construct(private RlstWalletOwner $model, RlstWallet $modelWallet)
    {
        $this->model = $model;
        $this->modelWallet = $modelWallet;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstWalletOwnerResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstWalletOwnerResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstWalletOwnerRequest $request)
    {
        foreach ($request['wallet-owner'] as $wallet_owner) {
            $model = $this->model->create($wallet_owner);
        }
        $model->refresh();
        return responseJson(200, 'success');
    }
    public function update($id, RlstWalletOwnerRequest $request)
    {
        $modelWallets = $this->modelWallet->find($id);
        if (!$modelWallets) {
            return responseJson(404, 'not found');
        }
        if ($modelWallets->walletOwner) {
            foreach ($modelWallets->walletOwner as $walletOwner) {
                $walletOwner->delete();
            }
        }
        if ($request['wallet-owner']) {
            foreach ($request['wallet-owner'] as $wallet_owner) {
                $model = $this->model->create($wallet_owner);
            }
            $model->refresh();
        }
        return responseJson(200, 'success');
    }
    // public function update($id, RlstWalletOwnerRequest $request)
    // {
    //     $modelWallets = $this->modelWallet->find($id);
    //     if (!$modelWallets) {
    //         return responseJson(404, 'not found');
    //     }
    //     foreach ($modelWallets->walletOwner as $walletOwner){
    //         $walletOwner->delete();
    //     }
    //     foreach ($request['wallet-owner'] as $wallet_owner){
    //         $model = $this->model->create($wallet_owner);
    //     }
    //     $model->refresh();
    //     return responseJson(200, 'success');
    // }

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
        $model->delete();
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
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }
}
