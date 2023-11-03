<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmSponser;
use Modules\ClubMembers\Http\Requests\CmSponserRequest;
use Modules\ClubMembers\Repositories\CmSponser\CmSponserInterface;
use Modules\ClubMembers\Transformers\CmSponserResource;
use Modules\ClubMembers\Transformers\CmSponsorDataResource;

class CmSponserController extends Controller
{

    public function __construct(private CmSponserInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmSponserResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getRootNodes()
    {
        return $this->modelInterface->getRootNodes();
    }
    public function getChildNodes($parentId)
    {
        return $this->modelInterface->getChildNodes($parentId);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmSponserResource($model));
    }

    public function create(CmSponserRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new CmSponserResource($model));
    }

    public function update(CmSponserRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmSponserResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        if ($model->hasChildren()) {
            return responseJson(400, __("this sponsor belongs to member and can't be deleted remove it's member first"));
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
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }

            $ss = [];
            if ($model->hasChildren()) {
                $ss[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        if (count($ss) > 0) {
            return responseJson(400, __('some sponsor has relation with member cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function getSponsors($group_id)
    {
        $models = CmSponser::where('group_id', $group_id)->get();
        return responseJson(200, 'success', CmSponsorDataResource::collection($models));
    }
    public function getSponsorsNullGroup()
    {
        $models = CmSponser::whereNull('group_id')->get();
        return responseJson(200, 'success', CmSponsorDataResource::collection($models));
    }
}
