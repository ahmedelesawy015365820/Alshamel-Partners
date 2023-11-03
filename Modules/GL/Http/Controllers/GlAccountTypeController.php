<?php

namespace Modules\GL\Http\Controllers;


use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\GL\Http\Requests\CreateGlAccountTypeRequest;
use Modules\GL\Http\Requests\EditGlAccountTypeRequest;
use Modules\GL\Repositories\GlAccountTypeRepositoryInterface;
use Modules\GL\Transformers\GlAccountTypeResource;


class GlAccountTypeController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(GlAccountTypeRepositoryInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = cacheGet('GlAccountType_' . $id);
        if (!$model) {
            $model = $this->modelInterface->find($id);
            if (!$model) {
                return responseJson(404, __('message.data not found'));
            } else {
                cachePut('GlAccountType_' . $id, $model);
            }
        }
        return responseJson(200, 'success', new GlAccountTypeResource($model));
    }

    public function index(Request $request)
    {
        if (count($_GET) == 0) {
            $models = cacheGet('GlAccountType');
            if (!$models) {
                $models = $this->modelInterface->all($request);
                cachePut('GlAccountType', $models);
            }
        } else {
            $models = $this->modelInterface->all($request);
        }

        return responseJson(200, 'success', GlAccountTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(CreateGlAccountTypeRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');
    }

    public function update(EditGlAccountTypeRequest $request, $id)
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
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }
}
