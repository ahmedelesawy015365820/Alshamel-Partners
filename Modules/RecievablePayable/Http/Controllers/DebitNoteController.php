<?php

namespace Modules\RecievablePayable\Http\Controllers;

use App\Traits\BulkDeleteTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\RecievablePayable\Http\Requests\CreateOpeningBalanceRequest;
use Modules\RecievablePayable\Http\Requests\DebitNoteRequest;
use Modules\RecievablePayable\Http\Requests\EditOpeningBalanceRequest;
use Modules\RecievablePayable\Repositories\RpDebitNoteInterface;
use Modules\RecievablePayable\Repositories\RpOpeningBalanceInterface;
use Modules\RecievablePayable\Transformers\DebitNoteResource;
use Modules\RecievablePayable\Transformers\OpeningBalanceResource;

class DebitNoteController extends Controller
{
    use BulkDeleteTrait;
    private $modelInterface;
    public function __construct(RpDebitNoteInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function show($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new DebitNoteResource($model));
    }

    public function index(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DebitNoteResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(DebitNoteRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return responseJson(200, 'success', new DebitNoteResource($model));
    }

    public function update(DebitNoteRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);
        return responseJson(200, 'success', new DebitNoteResource($model));
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
//        if ($model->hisChildren()){
//            return responseJson(404, 'some items has relation cant delete');
//        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }
}
