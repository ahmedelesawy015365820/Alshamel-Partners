<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Branch\GetNameBranchResource;
use App\Repositories\Branch\BranchRepositoryInterface;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public $repository;
    public $resource = BranchResource::class;
    public function __construct(BranchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $branches = $this->repository->getAllBranches($request);

        return responseJson(200, 'success', ($this->resource)::collection($branches['data']), $branches['paginate'] ? getPaginates($branches['data']) : null);
    }

    public function store(BranchRequest $request)
    {
        $this->repository->create($request->validated());
        return responseJson(200, __('done'));

    }

    public function show($id)
    {
        if ($branch = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($branch), 200);
        }
        return responseJson(404, __('not found'));
    }

    public function edit($id)
    {

    }

    public function update(BranchRequest $request, $id)
    {
        $branch = $this->repository->update($request, $id);
        $branch->refresh();
        return responseJson(200, __('updated'),new $this->resource($branch),200);
    }

    public function logs($id)
    {
        $model = $this->repository->find($id);

        if (!$model) {
            return responseJson(404, __('not found'));
        }
        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \response
     */


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


    public function processJsonData(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        try {
            $messages = $this->repository->processJsonData($data);
            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getDropDown(Request $request)
    {

        $models = $this->repository->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
