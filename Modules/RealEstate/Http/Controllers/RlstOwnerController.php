<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstOwner;
use Modules\RealEstate\Http\Requests\RlstOwnerRequest;
use Modules\RealEstate\Transformers\RlstOwnerResource;

class RlstOwnerController extends Controller
{

    public function __construct(private RlstOwner $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {

        //return $this->model->data()->find($id);
        //return $this->model->data()->find($id)->wallets()->pluck('rlst_wallet_owners.wallet_id');
        //return $this->model->data()->find($id)->distinct()->buildings();

        $model = $this->model->data()->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstOwnerResource($model));
    }

    public function all(Request $request)
    {
        
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        
        

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstOwnerResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstOwnerRequest $request)
    {

        $model = $this->model->create($request->validated());
        return $model;


        return responseJson(200, 'created', new RlstOwnerResource($model));

    }

    public function update($id, RlstOwnerRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new RlstOwnerResource($model));

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


        $relationsWithChildren = $model->hasChildren();

        //return $relationsWithChildren;

        if (sizeof($relationsWithChildren) > 0) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = "This item has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.";
            }
            return responseJson(400, $errorMessages);
        }

        $model->deleted_at = now();
        $model->save();

        return responseJson(200, 'success');
    }



    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->model->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $model->deleted_at = now();
            $model->save();
            
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

    public function ownerWalletPercentage($wallet_id, $owner_id)
    {
        $model = \Modules\RealEstate\Entities\RlstWalletOwner::where('wallet_id', $wallet_id)->where('owner_id', $owner_id)->first();
        if (!$model) {
            return responseJson(404, 'not found');
        }
        return responseJson(200, 'success', new \Modules\RealEstate\Transformers\RlstWalletOwnerResource($model));

    }

    public function getDropDown()
    {
        $models = $this->model->select('id', 'name','name_e')->get();


        return responseJson(200, 'success', $models);
    }
}
