<?php

namespace App\Http\Controllers\ClientType;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\ClientType\ClientTypeResource;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\ClientType\ClientTypeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientTypeController extends Controller
{
    public function __construct(ClientTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function all(Request $request)
    {
        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', ClientTypeResource::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }


    public function logs($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }


    public function getDropDown(Request $request)
    {
        $models = $this->repository->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
