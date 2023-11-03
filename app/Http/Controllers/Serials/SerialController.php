<?php

namespace App\Http\Controllers\Serials;

use App\Http\Controllers\Controller;
use App\Http\Requests\SerialRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\Serials\SerialResource;
use App\Models\Serial;
use App\Repositories\Serial\SerialRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SerialController extends Controller
{
    public $repository;
    public $resource = SerialResource::class;
    public function __construct(SerialRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function index(Request $request)
    {

        $serials = $this->repository->getAll($request);

        return responseJson(200, 'success', ($this->resource)::collection($serials['data']), $serials['paginate'] ? getPaginates($serials['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \response
     */
    public function store(SerialRequest $request)
    {

        return $model = $this->repository->create($request->validated());
        return responseJson(200, __('done'), new SerialResource($model));

    }

    public function find($id)
    {

        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new SerialResource($model));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \response
     */
    public function show($id)
    {
        if ($serial = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($serial), 200);
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
    public function update(SerialRequest $request, $id)
    {

        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->repository->update($request, $id);

        return responseJson(200, 'success', new SerialResource($model));


        // $data = [];
        // if ($request->company_id) {
        //     if (!DB::table('companies')->find($request->company_id)) {
        //         return responseJson(422, __('company does\'t exist'));
        //     }
        //     $data['company_id'] = $request->company_id;
        // }
        // if ($request->branch_id) {
        //     if (!DB::table('general_branches')->find($request->branch_id)) {
        //         return responseJson(422, __('branch does\'t exist'));
        //     }
        //     $data['branch_id'] = $request->branch_id;
        // }
        // if ($request->store_id) {
        //     if (!DB::table('general_stores')->find($request->store_id)) {
        //         return responseJson(422, __('branch does\'t exist'));
        //     }
        //     $data['store_id'] = $request->store_id;
        // }
        // if ($request->start_no) {
        //     $data['start_no'] = $request->start_no;
        // }
        // if ($request->perfix) {
        //     $data['perfix'] = $request->perfix;
        // }
        // if ($request->suffix) {
        //     $data['suffix'] = $request->suffix;
        // }
        // if ($request->restart_period) {
        //     $data['restart_period'] = $request->restart_period;
        // }
        // if ($request->is_default) {
        //     $data['is_default'] = $request->is_default;
        // }

        // $this->repository->update($data, $id);
        // return responseJson(200, __('updated'), new SerialResource($data));

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
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \response
     */
    // public function destroy($id)
    // {
    //     $model = $this->repository->find($id);
    //     if (!$model) {
    //         return responseJson(404, __('message.data not found'));
    //     }
    //     if ($model->hasChildren()) {
    //         return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
    //     }
    //     $this->repository->delete($id);
    //     return responseJson(200, __('deleted'));
    // }

    // public function bulkDelete(Request $request)
    // {
    //     foreach ($request->ids as $id) {
    //         $model = $this->repository->find($id);
    //         $arr = [];
    //         if ($model->hasChildren()) {
    //             $arr[] = $id;
    //             continue;
    //         }
    //         $this->repository->delete($id);
    //     }
    //     if (count($arr) > 0) {
    //         return responseJson(400, __('some items has relation can\'t delete'));
    //     }
    //     return responseJson(200, __('Done'));
    // }

    public function destroy($id)
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


    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->repository->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $this->repository->delete($id);
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
                }

                $errorMessages[] = implode(' ', $relationErrorMessages);
            }

            return responseJson(400, $errorMessages);
        }

        return responseJson(200, __('Done'));
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }

    public function getSerialByBranchId(Request $request)
    {

        $serial = Serial::where('branch_id', $request->branch_id)
            ->where(function ($q) use ($request) {
                $q->when($request->company_id, function ($q) use ($request) {
                    $q->where('company_id', $request->company_id);
                });
            })->latest()->first();

        if ($serial) {

            return responseJson(200, __('Done'), new $this->resource($serial), 200);
        }

        return responseJson(404, __('not found'));
    }

    public function getDropDown(Request $request)
    {

        $models = $this->repository->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
