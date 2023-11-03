<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\BulkDeleteTrait;
use Illuminate\Http\Request;
use Modules\Archiving\Repositories\DocumentStatusInterface;
use Modules\Archiving\Transformers\DocumentStatusResource;

class DocumentStatusController extends Controller
{
    use BulkDeleteTrait;

    public $modelInterface;
    public function __construct(DocumentStatusInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function index(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocumentStatusResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function store(Request $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new DocumentStatusResource($model));
    }

    public function destroy($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }

        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function update(Request $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new DocumentStatusResource($model));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
