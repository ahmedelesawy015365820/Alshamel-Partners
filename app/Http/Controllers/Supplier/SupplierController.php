<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\Supplier\SupplierResource;
use App\Repositories\Supplier\SupplierRepository;
use App\Traits\BulkDeleteTrait;
use App\Traits\CanDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierController extends Controller
{
    use BulkDeleteTrait, CanDeleteTrait;

    protected $repository;
    protected $resource = SupplierResource::class;

    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $models = $this->repository->all($request);

        return responseJson(200, 'success', ($this->resource)::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function show($id)
    {

        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, __('Done'), new $this->resource($model));

    }

    public function store(SupplierRequest $request)
    {
        $model = $this->repository->create($request->validated());
        return responseJson(200, 'success', new SupplierResource($model));
    }

    public function update(SupplierRequest $request, $id)
    {

        // $model = $this->repository->find($id);
        // if (!$model) {
        //     return responseJson(404, __('message.data not found'));
        // }
        // $model = $this->repository->update($request->validated(), $id);

        // return responseJson(200, __('Done'), new GeneralCustomerResource($model));

        $model = $this->repository->update($request, $id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new SupplierResource($model));

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

    public function delete($id)
    {
        $model = $this->repository->find($id);
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

        $this->repository->delete($id);

        return responseJson(200, 'success');
    }
    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }

    public function getDropDown(Request $request)
    {

        $models = $this->repository->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
