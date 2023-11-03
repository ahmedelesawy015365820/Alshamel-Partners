<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Http\Requests\CmMembershipRenewalRequest;
use Modules\ClubMembers\Http\Requests\EditCmMembershipRenewalRequest;
use Modules\ClubMembers\Repositories\CmMembershipRenewal\CmMembershipRenewalInterface;
use Modules\ClubMembers\Transformers\CmMembershipRenewalResource;

class CmMembershipRenewalController extends Controller
{

    public function __construct(private CmMembershipRenewalInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {


        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmMembershipRenewalResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmMembershipRenewalResource($model));
    }

    public function create(CmMembershipRenewalRequest $request)
    {
        $this->modelInterface->create($request->validated());

        return responseJson(200, 'success');
    }

    public function update(EditCmMembershipRenewalRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);

        return responseJson(200, 'success', $model);
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        };

        return responseJson(200, __('Done'));
    }

}
