<?php

namespace App\Http\Controllers\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\City\CityResource;
use App\Models\City;
use App\Repositories\City\CityRepositoryInterface;
use App\Traits\CanDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    use CanDeleteTrait;

    public $repository;
    public $resource = CityResource::class;
    public function __construct(CityRepositoryInterface $repository, private City $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function index(Request $request)
    {

        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', ($this->resource)::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \response
     */
    public function store(CityRequest $request)
    {

        if (!DB::table('general_countries')->find($request->country_id)) {
            return responseJson(404, __('country does\'t exist'));
        }
        if (!DB::table('general_governorates')->find($request->governorate_id)) {
            return responseJson(404, __('governorates does\'t exist'));
        }
        return $this->repository->create($request->validated());
        // return responseJson(200, __('done'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \response
     */
    public function show($id)
    {
        if ($city = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($city), 200);
        }
        return responseJson(404, __('not found'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \response
     */
    public function update(CityRequest $request, $id)
    {

        $data = [];
        if ($request->country_id) {
            if (!DB::table('general_countries')->find($request->country_id)) {
                return responseJson(422, __('countries does\'t exist'));
            }
            $data['country_id'] = $request->country_id;
        }

        if ($request->governorate_id) {
            if (!DB::table('general_governorates')->find($request->governorate_id)) {
                return responseJson(422, __('governorates does\'t exist'));
            }
            $data['governorate_id'] = $request->governorate_id;
        }
        $this->repository->update($request->validated(), $id);
        return responseJson(200, __('updated'));

    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $model->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->model->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->model->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    // public function destroy($id)
    // {
    //     $model = $this->repository->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }

    //     $relationsWithChildren = $model->hasChildren();

    //     if (!empty($relationsWithChildren)) {
    //         $errorMessages = [];
    //         foreach ($relationsWithChildren as $relation) {
    //             $relationName = $this->getRelationDisplayName($relation['relation']);
    //             $childCount = $relation['count'];
    //             $childIds = implode(', ', $relation['ids']);
    //             $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
    //         }
    //         return responseJson(400, $errorMessages);
    //     }

    //     $this->repository->delete($id);

    //     return responseJson(200, 'success');
    // }

    // public function bulkDelete(Request $request)
    // {
    //     $itemsWithRelations = [];

    //     foreach ($request->ids as $id) {
    //         $model = $this->repository->find($id);

    //         $relationsWithChildren = $model->hasChildren();
    //         if (!empty($relationsWithChildren)) {
    //             $itemsWithRelations[] = [
    //                 'id' => $id,
    //                 'relations' => $relationsWithChildren,
    //             ];
    //             continue;
    //         }

    //         $this->repository->delete($id);
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

    // private function getRelationDisplayName($relation)
    // {
    //     $displayableName = str_replace('_', ' ', $relation);
    //     return ucwords($displayableName);
    // }

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
        return $models = $this->repository->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
