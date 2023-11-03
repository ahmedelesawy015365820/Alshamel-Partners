<?php

namespace App\Http\Controllers\TreeProperty;

use App\Http\Requests\BulkCreatePropertyRequest;
use App\Http\Requests\TreeProperty\TreePropertyRequest;
use App\Http\Requests\TreeProperty\EditTreePropertyRequest;
use App\Http\Resources\AllDropListResource;
use App\Http\Resources\TreeProperty\TreePropertyResource;
use App\Repositories\TreeProperty\TreePropertyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TreePropertyController extends Controller
{
    private $modelInterface;
    public function __construct(TreePropertyRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {


        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new TreePropertyResource($model));
    }

    public function index(Request $request)
    {

        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', TreePropertyResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(TreePropertyRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(TreePropertyRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success');
    }

    public function setting(Request $request)
    {
        $model = $this->modelInterface->setting($request);

        return responseJson(200, 'success');

    }

    public function getSetting($user_id, $screen_id)
    {
        $model = $this->modelInterface->getSetting($user_id, $screen_id);
        return responseJson(200, 'success', new \App\Http\Resources\ScreenSetting\ScreenSettingResource($model));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

    public function destroy($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

    public function getRootNodes()
    {
        return $this->modelInterface->getRootNodes();
    }

    public function getChildNodes($parentId)
    {
        return $this->modelInterface->getChildNodes($parentId);
    }

    public function bulkCreate(BulkCreatePropertyRequest $request)
    {
        $rows = $request->all();
        foreach ($rows as $row) {
            \App\Models\TreeProperty::create($row);
        }
        return responseJson(200, 'success');
    }

    public function getDropDown(Request $request)
    {

        $models = $this->modelInterface->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

}
